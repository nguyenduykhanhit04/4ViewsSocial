<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminStoryResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu Story cho màn hình quản trị Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'content'     => $this->content,
            'media_url'   => $this->media_url,
            'media_type'  => $this->media_type,
            'author'      => new AdminUserResource($this->whenLoaded('user') ?? $this->user),
            'expires_at'  => $this->expires_at?->toIso8601String(),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
