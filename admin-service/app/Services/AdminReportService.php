<?php

namespace App\Services;

use App\Models\ViolenceWarning;

class AdminReportService
{
    /**
     * Lấy danh sách các báo cáo vi phạm nội dung.
     *
     * @param  int  $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getReports(int $limit = 15)
    {
        return ViolenceWarning::with(['user', 'post.user'])->latest()->paginate($limit);
    }
}
