<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class HttpResource extends JsonResource
{
    /**
     * Create a new resource instance.
     */
    public function __construct(
        mixed $resource,
        private readonly ?string $message = null,
    ) {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array{message: string|null, data?: mixed}
     */
    public function toArray(Request $request): array
    {
        $response = [
            'message' => $this->message,
        ];
        if ($this->resource !== null) {
            $data = $this->resource;
            $response['data'] = $data;
        }

        return $response;
    }
}
