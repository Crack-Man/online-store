<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\RegisterRequest;
use App\Services\Personal\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService)
    {
        $formData = $request->validated();

        $user = $authService->createUser($formData);

        Auth::login($user);
        
        return response()->json([
            'message' => 'User registered successfully',
            'data' => $formData
        ], 201);
    }
}
