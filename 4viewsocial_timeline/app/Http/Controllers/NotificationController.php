<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    /**
     * Lấy danh sách thông báo của người dùng hiện tại
     */
    public function getNotifications(Request $request)
    {
        try {
            // Lấy ID người dùng (Ưu tiên Auth, fallback về user_id từ request)
            $userId = Auth::id() ?? $request->user_id;

            if (!$userId) {
                return $this->responseApi->badRequest("User not authenticated");
            }

            // Truy vấn thông báo
            $notifications = Notification::where('user_id', $userId)
                ->with(['actor:id,full_name,user_name,avatar_url']) // Lấy thông tin người gây ra hành động
                ->orderBy('is_view', 'asc') // Thông báo chưa xem lên đầu
                ->orderBy('created_at', 'desc') // Thông báo mới nhất lên đầu
                ->get();

            // Đếm số thông báo chưa xem
            $unreadCount = $notifications->where('is_view', 0)->count();

            return $this->responseApi->success([
                'notifications' => $notifications,
                'unread_count'  => $unreadCount
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Đánh dấu một thông báo cụ thể là đã xem
     */
    public function markAsReadSingle($id)
    {
        try {
            $notification = Notification::find($id);
            if ($notification) {
                $notification->update(['is_view' => 1]);
                return $this->responseApi->success(['message' => 'Đã xem']);
            }
            return $this->responseApi->badRequest("Không tìm thấy thông báo");
        } catch (\Throwable $th) {
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Đánh dấu tất cả thông báo là đã xem
     */
    public function markAsRead(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->user_id;

            Notification::where('user_id', $userId)
                ->where('is_view', 0)
                ->update(['is_view' => 1]);

            return $this->responseApi->success(['message' => 'Đã đánh dấu đọc tất cả']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->responseApi->internalServerError();
        }
    }

    // Set device token for FCM
    public function setDeviceToken(Request $request)
    {
        try {
            // 1. Lấy ID người dùng (Kiểm tra xem Auth có lấy được không)
            $userId = Auth::id() ?? $request->user_id;
            $fcmToken = $request->fcmToken;

            if (!$userId) {
                return $this->responseApi->badRequest("Lỗi: Không tìm thấy User ID. Hãy kiểm tra Token Login.");
            }

            // 2. Tìm chính xác user đó
            $user = \App\Models\User::where('id', $userId)->first();

            if (!$user) {
                return $this->responseApi->badRequest("Không tìm thấy user trong DB với ID: " . $userId);
            }

            // 3. Thực hiện lưu
            $user->device_token = $fcmToken;
            $user->save(); // Dùng save() để đảm bảo Laravel thực hiện lệnh UPDATE sql

            // 4. Trả về thông tin để bạn soi ở tab Network -> Response
            return $this->responseApi->success([
                'status' => 'Ghi DB thành công',
                'user_id_vua_luu' => $user->id,
                'token_vua_luu' => $user->device_token
            ]);

        } catch (\Throwable $th) {
            return $this->responseApi->internalServerError($th->getMessage());
        }
    }

    /**
     * Hàm dùng chung để bắn Firebase ngay lập tức
     * Được khai báo là static để gọi từ Controller khác không cần khởi tạo
     */
    public static function sendPushNow($notification)
    {
        try {
            // 2. Lấy thông tin người nhận và người thực hiện
            $recipient = User::find($notification->user_id);
            $actor = User::find($notification->actor_id);

            // 3. Kiểm tra điều kiện: có token mới gửi
            if ($recipient && $recipient->device_token) {
                
                // Khởi tạo Service Firebase mà bạn đã viết
                $firebaseService = new FirebaseService();
                
                // Ghép nội dung: "Nguyễn Văn A đã thích bài viết của bạn."
                $fullContent = ($actor->full_name ?? 'Ai đó') . ' ' . $notification->content;
                
                // 4. GỌI HÀM sendFCM TRONG SERVICE CỦA BẠN
                $isSent = $firebaseService->sendFCM($fullContent, $recipient->device_token);

                // 5. Cập nhật trạng thái thông báo
                $notification->update([
                    'status' => $isSent ? Notification::STATUS_DONE : Notification::STATUS_FAIL
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Lỗi gửi Firebase: " . $e->getMessage());
        }
    }
}