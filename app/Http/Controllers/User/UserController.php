<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Requests\User\PaginationUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\HttpResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

final class UserController extends Controller
{
    public function index(PaginationUserRequest $request): JsonResponse
    {
        $orders = [];
        if ($request->has('orders')) {
            /** @var list<array{column: string, direction: string}> $orders */
            $orders = Str::pairList(
                (string) $request->input('orders'),
                'column',
                'direction',
            );
        }
        $data = User::query()
            ->orderable($orders)
            ->queryPagination();

        return response()->json($data);
    }

    public function store(StoreUserRequest $request): void {}

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function destroy(User $user): HttpResource
    {
        return new HttpResource(null, 'User has been deleted');
    }
}
