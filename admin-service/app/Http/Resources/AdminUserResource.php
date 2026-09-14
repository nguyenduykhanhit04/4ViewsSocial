<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu tài khoản người dùng cho màn hình quản trị Admin.
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
            'role'          => $this->role,
            'role_label'    => $this->role === 0 ? 'Admin' : 'Thành viên',
            'online_status' => $this->online_status,
            'status'        => $this->status,
            'status_label'  => $this->status === 0 ? 'Hoạt động' : 'Bị khóa',
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
