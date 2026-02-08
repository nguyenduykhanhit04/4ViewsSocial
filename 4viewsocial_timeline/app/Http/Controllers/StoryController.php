<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\LikeStory;
use App\Models\Notification;
use App\Models\Story;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    private $responseApi;

    public function __construct(ResponseApi $responseApi)
    {
        $this->responseApi = $responseApi;
    }

    /**
     * Lấy danh sách stories của người mà user đang follow
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listStory(Request $request)
    {
        $param = $request->all();
        $userId = $param['user_id'];
        $stories = DB::table('users')
            ->where('users.id', $userId)
            // Join bảng follow để biết user này follow ai
            ->join('follows', 'users.id', '=', 'follows.user_id')
            // Join bảng stories của người mà user đang follow
            ->join('stories', 'follows.following_id', '=', 'stories.user_id')
            // Join bảng users nữa để lấy thông tin người bạn
            ->join('users as friends', 'friends.id', '=', 'follows.following_id')
            ->leftJoin('like_stories', function ($join) use ($userId) {
                $join->on('stories.id', '=', 'like_stories.story_id')
                     ->where('like_stories.user_id', '=', $userId);
            })
            ->where('stories.expired_time', '>', now())
            ->select(
                'stories.*',
                'friends.user_name',
                'friends.full_name',
                'friends.avatar_url',
                'like_stories.id as is_liked'

            )
            ->get();
        return $this->responseApi->success($stories);
    }

    /**
     * Add a new story
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function addStory(Request $request)
    {
        try {
            $param = $request->all();
            $userId = $request->input('user_id');
            $destinationPath = public_path('stories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Lưu file video vào public/stories
            $videoFile = $request->file('file');
            $videoName = time() . '_' . $videoFile->getClientOriginalName();
            $videoFile->move($destinationPath, $videoName);

            Story::create([
                'user_id' => $userId,
                'video_url' => url('stories/' . $videoName),
                'expired_time' => now()->addHours(24)
            ]);
            return $this->responseApi->success();
        } catch (\Exception $e) {
            Log::error($e);
            return $this->responseApi->BadRequest($e->getMessage());
        }
        // $param = $request->input('user_id');
       // return $this->responseApi->success($param);
    }

    /**
     * Xóa story
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function deleteStory(Request $request)
    {
        try {
            $param = $request->all();
            Story::where('id', $param['story_id'])->delete();
            return $this->responseApi->success();
        } catch (\Exception $e) {
            return $this->responseApi->BadRequest($e->getMessage());
        }
    }


    public function likeStory(Request $request)
    {
        try {
            $param = $request->all();
            $storyId = $param['story_id'];
            $userId = $param['user_id'];
            $isLiked = $param['is_liked'];

            $story = Story::find($storyId);
            if (!$story) {
                return $this->responseApi->BadRequest('Story not found');
            }

            if ($isLiked) {
                // Nếu đã like, thì bỏ like
                LikeStory::create([
                    'story_id' => $storyId,
                    'user_id' => $userId
                ]);

                // Chỉ gửi nếu người like không phải chủ story
                if ($userId != $story->user_id) {
                    try {
                        $noti = Notification::create([
                            'user_id'  => $story->user_id, // Chủ story nhận thông báo
                            'actor_id' => $userId,        // Người like,
                            'story_id' => $storyId,
                            'type'     => Notification::NOTI_LIKE_STORY, // Giá trị = 3
                            'content'  => 'đã thích tin của bạn.',
                            'is_view'  => Notification::IS_NOT_VIEWED,
                            'status'   => Notification::STATUS_WAIT
                        ]);
                        // Gửi thông báo đẩy ngay
                        NotificationController::sendPushNow($noti);
                    } catch (\Exception $e) {
                        Log::error('Error creating notification for like story: ' . $e->getMessage());
                    }
                }
            } else {
                // Nếu chưa like, thì thêm like
                LikeStory::where('story_id', $storyId)
                    ->where('user_id', $userId)
                    ->delete();
            }

            return $this->responseApi->success();
        } catch (\Exception $e) {
            return $this->responseApi->BadRequest($e->getMessage());
        }
    }
}
