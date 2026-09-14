<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminReportResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu báo cáo vi phạm cho màn hình quản trị Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'post_id'     => $this->post_id,
            'reason'      => $this->reason ?? 'Vi phạm tiêu chuẩn cộng đồng',
            'status'      => $this->status ?? 'pending',
            'reporter'    => new AdminUserResource($this->whenLoaded('user') ?? $this->user),
            'post'        => new AdminPostResource($this->whenLoaded('post') ?? $this->post),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
