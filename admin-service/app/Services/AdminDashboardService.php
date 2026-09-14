<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use App\Models\ViolenceWarning;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Lấy dữ liệu thống kê tổng quan của hệ thống.
     *
     * @return array
     */
    public function getSystemStats(): array
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();
        $totalStories = Story::count();
        $totalComments = Comment::count();
        $totalReports = ViolenceWarning::count();

        // Thống kê người dùng mới trong 7 ngày gần nhất
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $newPostsThisWeek = Post::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            'total_users'         => $totalUsers,
            'total_posts'         => $totalPosts,
            'total_stories'       => $totalStories,
            'total_comments'      => $totalComments,
            'total_reports'       => $totalReports,
            'new_users_this_week' => $newUsersThisWeek,
            'new_posts_this_week' => $newPostsThisWeek,
        ];
    }

    /**
     * Lấy dữ liệu vẽ biểu đồ tăng trưởng người dùng và bài viết theo tháng.
     *
     * @return array
     */
    public function getGrowthChartData(): array
    {
        // Lấy thống kê 6 tháng gần nhất
        $months = [];
        $userCounts = [];
        $postCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthLabel = 'Tháng ' . $monthDate->format('m/Y');
            $months[] = $monthLabel;

            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            $userCounts[] = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $postCounts[] = Post::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Người dùng mới',
                    'data'  => $userCounts,
                ],
                [
                    'label' => 'Bài viết mới',
                    'data'  => $postCounts,
                ],
            ],
        ];
    }
}
