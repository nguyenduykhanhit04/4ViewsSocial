<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\AdminDashboardService;
use Illuminate\Http\JsonResponse;
use Exception;

class DashboardController extends Controller
{
    protected AdminDashboardService $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Lấy các chỉ số thống kê tổng quan của hệ thống.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getSystemStats();
            return ApiResponse::success($stats);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy thống kê hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Lấy dữ liệu biểu đồ tăng trưởng.
     */
    public function chart(): JsonResponse
    {
        try {
            $chartData = $this->dashboardService->getGrowthChartData();
            return ApiResponse::success($chartData);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy dữ liệu biểu đồ: ' . $e->getMessage());
        }
    }
}
