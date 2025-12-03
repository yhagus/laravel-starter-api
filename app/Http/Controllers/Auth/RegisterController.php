<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\HttpResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Routing\Controller;

#[Group('Authentication')]
final class RegisterController extends Controller
{
    public function store(RegisterRequest $request): HttpResource
    {
        User::query()->create([
            'first_name' => $request->string('first_name'),
            'last_name' => $request->string('last_name'),
            'email' => $request->string('email'),
            'password' => bcrypt($request->string('password')),
        ]);

        return new HttpResource(null, 'You have been registered successfully.');
    }
}
