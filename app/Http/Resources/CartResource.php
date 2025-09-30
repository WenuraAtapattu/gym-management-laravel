<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'item_count' => $this->item_count,
            'total' => $this->total,
            'formatted_total' => $this->formatted_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
