<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Post;
use App\Models\User;
use App\Models\ViolenceWarning;
use Illuminate\Http\Request;
use App\Models\Notification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    public function stats(Request $request)
    {
        $data = [
            'total_users'            => User::count(),
            'online_users'           => User::where('online_status', 1)->count(),
            'total_posts'            => Post::count(),
            'totalViolenceWarnings'  => ViolenceWarning::count(),
        ];

        return $this->response->success($data);
    }

    public function chart()
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $days = collect(range(6, 0))->map(fn ($i) =>
            Carbon::now()->subDays($i)->toDateString()
        );

        $users = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $posts = Post::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $violations = Notification::where('type', 2)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return response()->json([
            'user_growth' => [
                'labels' => $days->map(fn ($d) => Carbon::parse($d)->format('D'))->values(),
                'data'   => $days->map(fn ($d) => (int) ($users[$d] ?? 0))->values(),
            ],
            'post_stats' => [
                'labels'     => $days->map(fn ($d) => Carbon::parse($d)->format('D'))->values(),
                'posts'      => $days->map(fn ($d) => (int) ($posts[$d] ?? 0))->values(),
                'violations' => $days->map(fn ($d) => (int) ($violations[$d] ?? 0))->values(),
            ],
        ]);
    }
}
