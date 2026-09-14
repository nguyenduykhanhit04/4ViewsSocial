<?php

namespace App\Services;

use App\Models\User;

class AdminUserService
{
    /**
     * Lấy danh sách thành viên có phân trang và tìm kiếm.
     *
     * @param  string|null  $search
     * @param  int          $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUsers(?string $search = null, int $limit = 15)
    {
        $query = User::query()->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($limit);
    }

    /**
     * Lấy chi tiết thông tin một người dùng theo ID.
     *
     * @param  int  $id
     * @return \App\Models\User|null
     */
    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Khóa hoặc Mở khóa tài khoản thành viên.
     *
     * @param  int  $id
     * @return array
     */
    public function toggleUserStatus(int $id): array
    {
        $user = User::find($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        $newStatus = $user->status === User::STATUS_ACTIVE ? User::STATUS_BANNED : User::STATUS_ACTIVE;
        $user->status = $newStatus;
        $user->save();

        $statusText = $newStatus === User::STATUS_BANNED ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.';
        return ['success' => true, 'status' => $newStatus, 'message' => $statusText];
    }
}
