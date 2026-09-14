<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Chuyển đổi đối tượng người dùng thành mảng dữ liệu JSON trả về client.
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
            'email'         => $this->email,
            'avatar_url'    => $this->avatar_url,
            'bio'           => $this->bio,
            'role'          => $this->role,
            'online_status' => $this->online_status,
            'status'        => $this->status,
            'facebook_url'  => $this->facebook_url,
            'thread_url'    => $this->thread_url,
            'instagram_url' => $this->instagram_url,
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
