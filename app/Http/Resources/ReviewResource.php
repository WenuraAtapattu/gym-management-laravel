<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $isAdmin = $request->user()?->is_admin ?? false;
        $user = $this->whenLoaded('user');
        $reviewable = $this->whenLoaded('reviewable');

        $response = [
            'id' => (string) $this->_id,
            'reviewable_id' => (string) $this->reviewable_id,
            'reviewable_type' => $this->reviewable_type,
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'status' => $this->status ?? 'pending',
            'is_approved' => ($this->status ?? 'pending') === 'approved',
            'created_at' => $this->created_at?->diffForHumans() ?? now()->diffForHumans(),
            'created_at_raw' => $this->created_at?->toDateTimeString() ?? now()->toDateTimeString(),
            'can' => [
                'update' => $request->user()?->can('update', $this->resource),
                'delete' => $request->user()?->can('delete', $this->resource),
                'report' => $request->user()?->can('report', $this->resource),
            ],
        ];

        // Include user information
        if ($user) {
            $response['user'] = [
                'id' => $this->user_id ? (string) $this->user_id : null,
                'name' => $this->user?->name ?? $this->guest_name,
                'email' => $isAdmin ? ($this->user?->email ?? $this->guest_email) : null,
                'avatar' => $this->user?->profile_photo_url ?? null,
                'is_guest' => is_null($this->user_id),
            ];
        }

        // Include reviewable information if loaded
        if ($reviewable) {
            $response['reviewable'] = [
                'id' => (string) $this->reviewable_id,
                'name' => $reviewable->name ?? null,
                'type' => class_basename($this->reviewable_type),
                // Add other reviewable attributes as needed
            ];
        }

        // Include admin-only fields
        if ($isAdmin) {
            $response['ip_address'] = $this->ip_address ?? null;
            $response['user_agent'] = $this->user_agent ?? null;
            $response['updated_at'] = $this->updated_at?->toDateTimeString();
        }

        return $response;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function with($request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'api_version' => 'v1',
                'status' => 'success',
                'execution_time' => microtime(true) - LARAVEL_START,
            ],
        ];
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('X-Value', 'True');
    }
}