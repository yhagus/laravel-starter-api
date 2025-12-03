<?php

declare(strict_types=1);

namespace App\Http\Controllers\Role;

use App\Http\Requests\Role\PaginationRoleRequest;
use Illuminate\Routing\Controller;

final class RoleController extends Controller
{
    public function index(PaginationRoleRequest $request)
    {
        return response()->json([]);
    }

    public function show()
    {
        return response()->json([]);
    }

    public function store()
    {
        return response()->json([]);
    }

    public function update()
    {
        return response()->json([]);
    }

    public function destroy()
    {
        return response()->json([]);
    }
}
