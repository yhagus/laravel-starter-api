<?php

declare(strict_types=1);

namespace App\Http\Controllers\Fortify;

use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Fortify\Fortify;

#[Group('Authentication')]
final class ProfileInformationController extends Controller
{
    /**
     * Show the current user's profile information.
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();

        // Ensure the user is authenticated and is a User model instance.
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'email' => $user->email,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, UpdatesUserProfileInformation $updater): JsonResponse
    {
        $user = $request->user();

        // Ensure the user is authenticated and is a User model instance.
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => [
                'required',
                'string',
                'digits_between:10,15',
                // Use the validated user's ID to prevent issues with auth()->id() being null.
                Rule::unique('users', 'mobile_number')->ignore($user->id),
            ],
        ]);

        $usernameField = Fortify::username();
        if (config('fortify.lowercase_usernames') && $request->has($usernameField)) {
            $usernameValue = $request->input($usernameField);
            // Ensure the value is a string before lowercasing.
            if (is_string($usernameValue)) {
                $request->merge([
                    $usernameField => Str::lower($usernameValue),
                ]);
            }
        }

        $updater->update($user, $request->all());

        return response()->json([
            'message' => 'Profile updated.',
        ]);
    }
}
