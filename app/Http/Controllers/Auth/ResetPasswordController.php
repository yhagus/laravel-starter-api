<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatus;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\HttpResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

#[Group('Authentication')]
final class ResetPasswordController extends Controller
{
    /**
     * Send forgot password
     *
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request): HttpResource
    {
        $status = Password::broker()->sendResetLink([
            'email' => (string) $request->string('email'),
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return new HttpResource(null, __($status));
        }

        abort(HttpStatus::HTTP_BAD_REQUEST, __($status));
    }

    /**
     * Reset password
     *
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordRequest $request): HttpResource
    {
        $status = Password::broker()->reset(
            [
                'email' => (string) $request->string('email'),
                'password' => (string) $request->string('password'),
                'password_confirmation' => (string) $request->string('password_confirmation'),
                'token' => (string) $request->string('token'),
            ],
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        abort_if($status !== Password::PASSWORD_RESET, HttpStatus::HTTP_BAD_REQUEST, __($status));

        return new HttpResource(null, __($status));
    }
}
