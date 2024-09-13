<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pharmacist;
use App\Models\Pharmacy;
use App\Models\Warehouse;
use App\Models\keeper;
use Illuminate\Support\Str;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Generate a new token
        $token = Str::random(80);
        // Store the token in the `api_token` column for the user
        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();
        $user_agent = $request->header('User-Agent');
        if ($user_agent == "warehouse keeper") {
            return response()->json([
                'message' => "Welcome Back to website {$request->name} ",
                'token' => $token
            ]);
        } else {
            return response()->json([
                'message' => "Welcome Back to app {$request->name}",
                'token' => $token
            ]);
        }
    }
    public function register(Request $request)
    {
        $user_agent = $request->header('User-Agent');
        if ($user_agent == "warehouse keeper") {
            $user = new keeper();
            $user->name = $request->input('name');
            $user->phone_number = $request->input('phone_number');
            $user->password = $request->input('password');

            // Generate a new token
            $token = Str::random(80);

            // Store the token in the `api_token` column for the user
            $user->api_token = hash('sha256', $token);
            $warehouse=Warehouse::where('name',$request->input('warehouse_name'))->first();
            if(!$warehouse){
                $warehouse = new Warehouse();
                $warehouse->name = $request->input('warehouse_name');
                $warehouse->save();
                $user->warehouse_id = $warehouse->id;
                $user->save();
            }
            else{
                $user->warehouse_id = $warehouse->id;
                $user->save();
            }
            

            return response()->json([
                'message' => "Welcome to website {$user->name}",
                'token' => $token
            ]);
        } else {
            
            $user = new Pharmacist();
            $user->name = $request->input('name');
            $user->phone_number = $request->input('phone_number');
            $user->password = $request->input('password');


            // Generate a new token
            $token = Str::random(80);

            // Store the token in the `api_token` column for the user
            $user->api_token = hash('sha256', $token);
            $pharmacy=Pharmacy::where('name',$request->input('pharnacy_name'))->first();
            if(!$pharmacy){
                $pharmacy = new Pharmacy();
                $pharmacy->name = $request->input('pharmacy_name');
                $pharmacy->save();
                $user->pharmacy_id = $pharmacy->id;
                $user->save();
            }
            else{
                $pharmacy->save();
                $user->pharmacy_id = $pharmacy->id;
                $user->save();
            }
            

            return response()->json([
                'message' => "Welcome to app {$user->name}",
                'token' => $token
            ]);
        }
    }
}
