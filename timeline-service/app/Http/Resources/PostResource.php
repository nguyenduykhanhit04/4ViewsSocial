<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu bài viết sang định dạng JSON chuẩn.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'content'        => $this->content,
            'image_url'      => $this->image_url,
            'video_url'      => $this->video_url,
            'location'       => $this->location,
            'total_likes'    => $this->total_likes ?? ($this->likes_count ?? 0),
            'total_comments' => $this->total_comments ?? ($this->comments_count ?? 0),
            'is_liked'       => (bool) ($this->is_liked ?? false),
            'is_saved'       => (bool) ($this->is_saved ?? false),
            'user'           => new UserProfileResource($this->whenLoaded('user') ?? $this->user),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
