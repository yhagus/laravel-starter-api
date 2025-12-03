<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait Sortable
{
    /**
     * Apply multiple order statements to the query.
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, string>|list<array{column: string, direction: string}>  $orders
     * @return Builder<TModel>
     */
    protected function scopeOrderable(Builder $query, array $orders = []): Builder
    {
        $normalizedOrders = $this->normalizeOrders($orders);
        if ($normalizedOrders === []) {
            return $query;
        }
        foreach ($normalizedOrders as $order) {
            $column = $order['column'];
            if (! is_string($column)) {
                continue;
            }
            if ($column === '') {
                continue;
            }
            $orderDirection = mb_strtolower((string) $order['direction']) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($column, $orderDirection);
        }

        return $query;
    }

    /**
     * @param  array<string, string>|list<array{column: string, direction: string}>  $orders
     * @return list<array{column: string, direction: string}>
     */
    private function normalizeOrders(array $orders): array
    {
        if ($orders === []) {
            return [];
        }
        $normalized = [];
        if (! array_is_list($orders)) {
            foreach ($orders as $column => $direction) {
                $normalized[] = [
                    'column' => (string) $column,
                    'direction' => (string) $direction,
                ];
            }

            return $normalized;
        }
        foreach ($orders as $order) {
            if (! is_array($order)) {
                continue;
            }
            $column = $order['column'] ?? null;
            $direction = $order['direction'] ?? null;
            if (! is_string($column)) {
                continue;
            }
            if (! is_string($direction)) {
                continue;
            }
            $normalized[] = [
                'column' => $column,
                'direction' => $direction,
            ];
        }

        return $normalized;
    }
}
