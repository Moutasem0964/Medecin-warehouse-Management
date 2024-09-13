<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class register_check
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required|regex:/^09[0-9]{8}$/|unique:keepers,phone_number|unique:pharmacists,phone_number',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'wrong credential',
                'errors' => $validator->errors()
            ]);
        }
        return $next($request);
    }
}
