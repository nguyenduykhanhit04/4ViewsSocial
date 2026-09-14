<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPostResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu bài viết cho màn hình quản trị Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'caption'        => $this->caption,
            'thumbnail_url'  => $this->thumbnail_url,
            'total_like'     => $this->total_like ?? 0,
            'total_comment'  => $this->total_comment ?? 0,
            'author'         => new AdminUserResource($this->whenLoaded('user') ?? $this->user),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
