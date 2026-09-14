<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu thông báo sang định dạng JSON chuẩn.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'sender_id'  => $this->sender_id,
            'type'       => $this->type,
            'content'    => $this->content,
            'target_id'  => $this->target_id,
            'is_read'    => (bool) ($this->is_read ?? false),
            'sender'     => new UserProfileResource($this->whenLoaded('sender') ?? $this->sender),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
