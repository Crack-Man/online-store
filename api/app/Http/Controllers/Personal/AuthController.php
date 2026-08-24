<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\RegisterRequest;
use App\Services\Personal\AuthService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v2/auth/sign-up',
        summary: 'Register user',
        description: 'Creates a user and starts a browser session. Swagger UI automatically obtains a CSRF cookie before sending this request.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'gender'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Антон'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@mail.ru'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'my_Password!1725'),
                    new OA\Property(property: 'gender', type: 'string', enum: ['male', 'female'], example: 'male'),
                ],
                type: 'object'
            )
        ),
        tags: ['Личный кабинет'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'Антон'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@mail.ru'),
                                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'my_Password!1725'),
                                new OA\Property(property: 'gender', type: 'string', enum: ['male', 'female'], example: 'male'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 419,
                description: 'CSRF token mismatch'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request, AuthService $authService)
    {
        $formData = $request->validated();

        $user = $authService->createUser($formData);

        Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $formData,
        ], 201);
    }
}
