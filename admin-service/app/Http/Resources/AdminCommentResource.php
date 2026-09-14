<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminCommentResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu bình luận cho quản trị Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'post_id'    => $this->post_id,
            'user_id'    => $this->user_id,
            'content'    => $this->content,
            'author'     => new AdminUserResource($this->whenLoaded('user') ?? $this->user),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
