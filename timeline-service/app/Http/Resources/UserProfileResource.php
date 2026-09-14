<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu tác giả / người dùng sang định dạng JSON an toàn.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_name'     => $this->user_name,
            'full_name'     => $this->full_name,
            'avatar_url'    => $this->avatar_url,
            'bio'           => $this->bio,
            'role'          => $this->role,
            'online_status' => $this->online_status,
            'facebook_url'  => $this->facebook_url,
            'thread_url'    => $this->thread_url,
            'instagram_url' => $this->instagram_url,
            'is_following'  => (bool) ($this->is_following ?? false),
        ];
    }
}
