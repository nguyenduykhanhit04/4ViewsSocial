<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu tin 24h sang định dạng JSON chuẩn.
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
            'media_url'   => $this->media_url ?? $this->image_url ?? $this->video_url,
            'media_type'  => $this->media_type ?? 'image',
            'is_liked'    => (bool) ($this->is_liked ?? false),
            'user'        => new UserProfileResource($this->whenLoaded('user') ?? $this->user),
            'expires_at'  => $this->expires_at?->toIso8601String(),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
