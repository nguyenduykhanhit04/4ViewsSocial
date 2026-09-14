<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AdminReportResource;
use App\Services\AdminReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ReportController extends Controller
{
    protected AdminReportService $reportService;

    public function __construct(AdminReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Lấy danh sách các báo cáo vi phạm nội dung.
     */
    public function listReports(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->input('limit', 15);
            $reports = $this->reportService->getReports($limit);

            return ApiResponse::success(AdminReportResource::collection($reports));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách báo cáo: ' . $e->getMessage());
        }
    }
}
