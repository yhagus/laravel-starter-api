<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatus;
use App\Events\UserLoggedIn;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\HandlesUserSessionCache;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Group('Authentication')]
final class LoginController extends Controller
{
    use HandlesUserSessionCache;

    /**
     * Login
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $baseUrl = (string) config('passport.base_url');
        $clientId = (string) config('passport.password_client_id');
        $clientSecret = (string) config('passport.password_client_secret');
        $email = (string) $request->string('email');
        $cachedSession = $this->getCachedUserSession($email);
        if ($cachedSession !== null) {
            event(new UserLoggedIn());

            return $this->respondWithUserSessionPayload($cachedSession, $email);
        }

        $user = User::query()->where('email', $email)->first();

        abort_if($user === null, HttpStatus::HTTP_UNAUTHORIZED, 'Unauthorized.');

        $password = $request->string('password');
        try {
            /** @var HttpResponse $response */
            $response = Http::asForm()->post($baseUrl.'/vendor/passport/token', [
                'grant_type' => 'password',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'username' => $email,
                'password' => $password,
            ]);
        } catch (Exception $exception) {
            $exceptionMessage = 'Error generating token: '.$exception->getMessage();
            Log::error($exceptionMessage);
            abort(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $exceptionMessage);
        }
        if ($response->failed()) {
            return response()->json($response->body(), $response->status());
        }
        /** @var array{token_type: string, expires_in: int, access_token: string, refresh_token: string}|null $jsonResponse */
        $jsonResponse = $response->json();
        if ($jsonResponse !== null) {
            $this->cacheUserSession($email, $jsonResponse);
        }

        event(new UserLoggedIn());

        return $this->respondWithUserSessionPayload($jsonResponse ?? [], $email);
    }
}
