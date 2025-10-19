<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasQueryPagination
{
    protected function scopeQueryPagination(
        Builder $query,
        ?string $search = null,     // Will be overridden if null
        ?int $page = null,          // Will be overridden if null
        ?int $perPage = null,       // Will be overridden if null
        array $searchableFields = ['name']
    ): array {
        // Use the provided parameter, or fall back to the request query string
        $search ??= request()->query('search');
        $page ??= (int) request()->query('page'); // Cast to int for simplePaginate if exists
        $perPage ??= (int) request()->query('per_page', 25); // Default to 25 if not in request

        // Apply search query if provided
        if ($search) {
            // Convert the search term to lowercase once
            $lowerCaseSearch = mb_strtolower($search);

            $query->where(function (Builder $q) use ($lowerCaseSearch, $searchableFields): void {
                if ($searchableFields === []) {
                    return;
                }

                $firstField = array_shift($searchableFields);
                // Apply LOWER() to the database column
                $q->where(DB::raw("LOWER({$firstField})"), 'like', '%'.$lowerCaseSearch.'%');

                foreach ($searchableFields as $field) {
                    // Apply LOWER() to subsequent columns
                    $q->orWhere(DB::raw("LOWER({$field})"), 'like', '%'.$lowerCaseSearch.'%');
                }
            });
        }
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
