<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use App\Models\Story;

class StoryController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    public function listStories(Request $request)
    {
        $query = Story::select(
                'stories.id',
                'stories.user_id',
                'users.full_name as user_name',
                'stories.video_url',
                'stories.expired_time',
                'stories.created_at'
            )
            ->join('users', 'users.id', '=', 'stories.user_id');

        if ($request->video_url) {
            $query->where('stories.video_url', 'like', '%' . $request->video_url . '%');
        }

        if ($request->user_id) {
            $query->where('stories.user_id', $request->user_id);
        }

        if ($request->active == 1) {
            $query->where('stories.expired_time', '>', now());
        }

        $stories = $query->orderBy('stories.id', 'desc')->get();

        if ($stories->isEmpty()) {
            return $this->response->dataNotfound();
        }

        return $this->response->success($stories);
    }

    public function deleteStory($id)
    {
        if (!is_numeric($id)) {
            return $this->response->BadRequest("ID không hợp lệ");
        }

        $story = Story::find($id);

        if (!$story) {
            return $this->response->dataNotfound();
        }

        $story->delete();

        return $this->response->success([], "Đã xóa story thành công");
    }
}
