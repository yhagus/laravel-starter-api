<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @template TModel of Model
 */
trait HasQueryPagination
{
    /**
     * Apply pagination and search to a query.
     *
     * This scope automatically reads 'search', 'page', and 'per_page' from the request
     * if they are not passed as arguments.
     *
     * @param  Builder<TModel>  $query
     * @param  list<string>  $searchableFields  A list of column names to search against.
     * @return array{
     * data: array<int, TModel>,
     * meta: array{
     * current_page: int,
     * next_page_url: string|null,
     * prev_page_url: string|null,
     * per_page: int,
     * last_page: int,
     * total: int
     * }
     * }
     */
    protected function scopeQueryPagination(
        Builder $query,
        ?string $search = null,
        ?int $page = null,
        ?int $perPage = null,
        array $searchableFields = ['name']
    ): array {
        // 1. Resolve parameters from the request if not provided, with sane defaults.
        $requestSearch = request()->query('search');
        $search ??= is_string($requestSearch) ? $requestSearch : null;
        $page ??= (int) request()->query('page', 1);
        $perPage ??= (int) request()->query('per_page', 25);

        // Ensure page and perPage are positive integers.
        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 1) {
            $perPage = 25;
        }
        if ($search && ! empty($searchableFields)) {
            $lowerCaseSearch = mb_strtolower($search);
            $query->where(function (Builder $q) use ($lowerCaseSearch, $searchableFields): void {
                $fields = $searchableFields; // Work on a copy to avoid side effects.
                $firstField = array_shift($fields);
                $q->where(DB::raw("LOWER(`{$firstField}`)"), 'like', '%'.$lowerCaseSearch.'%');
                foreach ($fields as $field) {
                    $q->orWhere(DB::raw("LOWER(`{$field}`)"), 'like', '%'.$lowerCaseSearch.'%');
                }
            });
        }
        /** @var LengthAwarePaginator<int, TModel> $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
