<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }
    public function getProfile(Request $request)
    {
        $userId = $request->input('user_id');
        // Lấy thông tin cơ bản của user này
        $user = DB::table('users')
            ->where('id', $userId)
            ->select('id', 'user_name', 'full_name', 'avatar_url', 'bio')
            ->first();

        if (!$user) return $this->responseApi->badRequest("User không tồn tại");

        $offset = $request->input('offset', 0); // Mặc định bắt đầu từ 0
        $limit = 20;

        // Lấy tổng số lượng (chỉ cần lấy 1 lần hoặc lấy kèm để FE biết khi nào dừng)
        $countFollowers = DB::table('follows')->where('following_id', $userId)->count();
        $countFollowing = DB::table('follows')->where('user_id', $userId)->count();
        $countPosts = DB::table('posts')->where('user_id', $userId)->count();

        // Lấy danh sách followers có phân trang
        $followers = DB::table('users')
            ->join('follows', 'users.id', '=', 'follows.user_id')
            ->where('follows.following_id', $userId)
            ->select('users.id', 'users.user_name', 'users.full_name', 'users.avatar_url')
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Tương tự cho following
        $following = DB::table('users')
            ->join('follows', 'users.id', '=', 'follows.following_id')
            ->where('follows.user_id', $userId)
            ->select('users.id', 'users.user_name', 'users.full_name', 'users.avatar_url')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return $this->responseApi->success([
            'user' => $user,
            'count_followers' => $countFollowers,
            'count_following' => $countFollowing,
            'count_posts' => $countPosts,
            'followers' => $followers,
            'following' => $following,
        ]);
    }

    // ProfileController.php

public function getPosts(Request $request)
{
    $userId = $request->input('user_id');
    $offset = $request->input('offset', 0);
    $limit = 12;

    $posts = DB::table('posts')
        ->where('user_id', $userId)
        ->whereNull('deleted_at') // Đảm bảo không lấy bài đã xóa
        ->select(
            'id', 
            'thumbnail_url', 
            'caption', 
            'total_like',     // Lấy số lượng like
            'total_comment',  // Lấy số lượng cmt
            'created_at'
        )
        ->orderBy('created_at', 'desc')
        ->offset($offset)
        ->limit($limit)
        ->get();

    return $this->responseApi->success($posts);
}

public function getPostSaved(Request $request)
{
    $userId = $request->input('user_id');
    $offset = $request->input('offset', 0);
    $limit = 12;

    $savedPosts = DB::table('posts')
        ->join('favourites', 'posts.id', '=', 'favourites.post_id')
        ->where('favourites.user_id', $userId)
        ->whereNull('posts.deleted_at')
        ->select(
            'posts.id', 
            'posts.thumbnail_url', 
            'posts.caption', 
            'posts.total_like',    // Lấy số lượng like
            'posts.total_comment', // Lấy số lượng cmt
            'posts.created_at'
        )
        ->orderBy('favourites.created_at', 'desc') // Sắp xếp theo thời gian lưu
        ->offset($offset)
        ->limit($limit)
        ->get();

    return $this->responseApi->success($savedPosts);
}

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'user_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'bio'       => 'nullable|string|max:150',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return $this->responseApi->badRequest($validator->errors()->first());
        }

        $userId = $request->input('user_id');
        $updateData = [
            'user_name' => $request->input('user_name'),
            'full_name' => $request->input('full_name'),
            'bio'       => $request->input('bio'),
        ];

        // Xử lý upload ảnh đại diện vào public/uploads/avatars
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // Tạo tên file duy nhất để tránh trùng lặp
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Định nghĩa đường dẫn đích trong thư mục public
            $destinationPath = public_path('uploads/avatars');

            // Di chuyển file vào thư mục public/uploads/avatars
            $file->move($destinationPath, $fileName);

            // Lưu URL đầy đủ vào database
            // asset() sẽ tự động tạo URL dựa trên APP_URL trong file .env
            $updateData['avatar_url'] = 'http://localhost:8004/'. 'uploads/avatars/' . $fileName;
        }

        // Cập nhật Database
        DB::table('users')->where('id', $userId)->update($updateData);

        // Lấy lại thông tin user mới nhất
        $user = DB::table('users')->where('id', $userId)->first();

        return $this->responseApi->success($user);
    }
    // --- ĐỔI MẬT KHẨU ---
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        // Trả về badRequest (mã 400) nếu validate sai định dạng
        if ($validator->fails()) {
            return $this->responseApi->badRequest($validator->errors()->first());
        }

        $userId = $request->input('user_id');
        $user = DB::table('users')->where('id', $userId)->first();

        // 1. Kiểm tra mật khẩu cũ có đúng không
        if (!Hash::check($request->input('old_password'), $user->password)) {
            // Sử dụng unauthorized (mã 401) cho trường hợp sai mật khẩu
            return $this->responseApi->unauthorized("Mật khẩu cũ không chính xác.");
        }

        // 2. Cập nhật mật khẩu mới (phải Hash trước khi lưu)
        DB::table('users')->where('id', $userId)->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return $this->responseApi->success(null);
    }
}
