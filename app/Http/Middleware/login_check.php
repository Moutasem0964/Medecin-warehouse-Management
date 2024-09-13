<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pharmacist;
use App\Models\keeper;
use App\Models\Keepr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class login_check
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if($request->input("phone_number")==null||$request->input("password")==null){
                Log::info('Missing phone number or password');
                return $this->errorResponse();
            }

            $user_agent=$request->header('User-Agent');
            if($user_agent=="pharmacist"){
                $user=Pharmacist::where('phone_number',$request->input('phone_number'))->first();}
            else{
                $user=Keeper::where('phone_number',$request->input('phone_number'))->first();   
            }

            if(!$user){
                Log::info('User not found');
                return $this->errorResponse();
            }

            $password=$user->password;

            if($request->input('password')!=$password){
                Log::info('Incorrect password');
                return $this->errorResponse();
            }

            $request->merge(['name'=>$user->name]);
        } catch(\Exception $e) {
            Log::error('Exception caught: ' . $e->getMessage());
            return $this->errorResponse();
        }
        auth()->login($user);

        return $next($request);
    }

    private function errorResponse()
    {
        return response()->json([
            'message'=>'wrong credentials'
        ]);
    }
}
