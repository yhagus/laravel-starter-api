<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\HttpResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json($user);
    }

    public function update(UpdateProfileRequest $request): HttpResource
    {
        return new HttpResource(null, 'Profile has been updated');
    }
}
