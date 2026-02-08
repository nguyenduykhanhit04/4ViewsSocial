<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $response;
    
    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    public function listUsers(Request $request)
    {
        $query = User::select(
            'id',
            'user_name',
            'full_name',
            'email',
            'role',
            'online_status',
            'status'
        );

        if ($request->name) {
            $query->where(function ($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->name . '%')
                  ->orWhere('full_name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->role !== null) {
            $query->where('role', $request->role);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('id', 'desc')->get();

        if ($users->isEmpty()) {
            return $this->response->dataNotfound();
        }

        return $this->response->success($users);
    }

    public function getUserById($id)
    {
        if (!is_numeric($id)) {
            return $this->response->BadRequest("ID không hợp lệ");
        }

        $user = User::find($id);

        if (!$user) {
            return $this->response->dataNotfound();
        }

        return $this->response->success($user);
    }
}
