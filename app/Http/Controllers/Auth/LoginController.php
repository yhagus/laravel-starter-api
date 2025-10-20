<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Group('Authentication')]
final class LoginController extends Controller
{
    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        /** @var string $baseUrl */
        $baseUrl = config('passport.base_url');
        /** @var string $clientId */
        $clientId = config('passport.password_client_id');
        /** @var string $clientSecret */
        $clientSecret = config('passport.password_client_secret');

        /** @var string $email */
        $email = $request->input('email');
        /** @var string $password */
        $password = $request->input('password');

        $response = Http::asForm()->post($baseUrl.'/api/vendor/passport/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]);

        if ($response->failed()) {
            return response()->json($response->body(), $response->status());
        }

        /** @var array<string, mixed>|null $jsonResponse */
        $jsonResponse = $response->json();

        return response()->json($jsonResponse ?? []);
    }
}
