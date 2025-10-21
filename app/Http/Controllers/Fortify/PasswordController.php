<?php

declare(strict_types=1);

namespace App\Http\Controllers\Fortify;

use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

#[Group('Authentication')]
final class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request, UpdatesUserPasswords $updater): JsonResponse
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $updater->update($user, $request->validated());
        $this->broker()->deleteToken($user);
        event(new PasswordUpdatedViaController($user));

        return response()->json([
            'message' => 'Password updated.',
        ]);
    }

    /**
     * Get the broker to be used to delete any existing password reset tokens.
     */
    private function broker(): PasswordBroker
    {
        $brokerConfig = config('fortify.passwords');
        $brokerName = is_string($brokerConfig) ? $brokerConfig : null;

        return Password::broker($brokerName);
    }
}
