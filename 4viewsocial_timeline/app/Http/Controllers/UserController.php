<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    /**
     * Suggest friends for a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function suggestFriend(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->user_id;

            // Danh sách ID người mà user đã follow
            $followedIds = Follow::where('user_id', $userId)
                ->pluck('following_id')
                ->toArray();

            // Gợi ý user: không phải chính mình, không nằm trong danh sách đã follow
            $suggestions = User::where('id', '!=', $userId)
                ->where('role', User::ROLE_CLIENT)
                ->whereNotIn('id', $followedIds)
                ->select('id', 'full_name', 'user_name', 'avatar_url', 'bio')
                ->inRandomOrder()
                ->limit(6)
                ->get();

            return $this->responseApi->success([
                'total' => $suggestions->count(),
                'users' => $suggestions
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Function follow a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function follow(Request $request)
    {
        try {
            $userId = $request->user_id ?? Auth::id(); 
            $followingId = $request->following_id;

            if (!$userId || !$followingId) {
                return response()->json(['message' => 'Thiếu ID'], 400);
            }

            $userToFollow = User::find($followingId);
            if (!$userToFollow) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            // Lấy thông tin người thực hiện (actor) để lấy tên hiển thị nếu cần
            $actor = User::find($userId);

            $follow = Follow::where('user_id', $userId)
                ->where('following_id', $followingId)
                ->first();

            if ($follow) {
                $follow->delete();
                // Tùy chọn: Xóa thông báo khi unfollow
                Notification::where('user_id', $followingId)
                    ->where('actor_id', $userId)
                    ->where('type', Notification::NOTI_FOLLOW)
                    ->delete();

                return $this->responseApi->success(['message' => 'Unfollowed']);
            }

            // 1. Tạo mới Follow
            $newFollow = new Follow();
            $newFollow->user_id = $userId;
            $newFollow->following_id = $followingId;
            $newFollow->save();

            // 2. Tạo thông báo cho người được follow
            // Tránh tự gửi thông báo cho chính mình nếu có lỗi logic
            if ($userId != $followingId) {
                try {
                    $noti = Notification::create([
                        'user_id'  => $followingId,           // Người nhận (người được follow)
                        'actor_id' => $userId,                // Người gây ra hành động (người bấm follow)
                        'type'     => Notification::NOTI_FOLLOW,
                        'content'  => 'đã bắt đầu theo dõi bạn.', // Hoặc chỉ để "Theo dõi" tùy bạn
                        'is_view'  => Notification::IS_NOT_VIEWED,                      // Chưa xem
                        'status'   => Notification::STATUS_WAIT   
                    ]);
                    // Gửi thông báo đẩy ngay
                    NotificationController::sendPushNow($noti);
                } catch (\Throwable $th) {
                    Log::error('Lỗi khi tạo thông báo follow: ' . $th->getMessage());
                }
            }

            return $this->responseApi->success(['message' => 'Followed']);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Lấy thông tin người dùng
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getInfo(Request $request)
    {
        $param = $request->all();
        $userId = $param['user_id'] ?? Auth::id();
    }

    public function suggestFriendByKey(Request $request)
    {
        try {
            $userId = $request->user_id ?? Auth::id();
           

            // Danh sách ID người mà user đã follow
            $followedIds = Follow::where('user_id', $userId)
                ->pluck('following_id')
                ->toArray();

            // Gợi ý user: không phải chính mình, không nằm trong danh sách đã follow, và khớp từ khóa
            $suggestions = User::where('id', '!=', $userId)
                ->where('role', User::ROLE_CLIENT)
                ->whereNotIn('id', $followedIds)
              
                ->select('id', 'full_name', 'user_name', 'avatar_url', 'bio')
                ->inRandomOrder()
                ->limit(6)
                ->get();

            return $this->responseApi->success([
                'total' => $suggestions->count(),
                'users' => $suggestions
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    public function getUsersInfo(Request $request)
    {
        try {
            $userIds = $request->input('user_ids', []);

            if (empty($userIds) || !is_array($userIds)) {
                return $this->responseApi->badRequest('Danh sách user_ids không hợp lệ.');
            }

            $users = User::whereIn('id', $userIds)
                ->select('id', 'full_name', 'user_name', 'avatar_url', 'bio')
                ->get();

            return $this->responseApi->success(['users' => $users]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    public function searchUser(Request $request)
    {
        try {
            $keyword = $request->input('keyword', '');

            if (empty($keyword)) {
                return $this->responseApi->badRequest('Từ khóa tìm kiếm không được để trống.');
            }

            $users = User::where('full_name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('user_name', 'LIKE', '%' . $keyword . '%')
                ->select('id', 'full_name', 'user_name', 'avatar_url', 'bio')
                ->get();

            return $this->responseApi->success(['users' => $users]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }
}
