<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Favourite;
use App\Models\LikePost;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService;
use App\Http\Controllers\NotificationController;

class PostController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    /**
     * List all posts with user information.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function listPost(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->query('user_id');

            $listPost = Post::leftJoin('users', 'users.id', '=', 'posts.user_id')

                // JOIN LIKE
                ->leftJoin('like_posts', function ($join) use ($userId) {
                    $join->on('like_posts.post_id', '=', 'posts.id')
                        ->where('like_posts.user_id', '=', $userId);
                })

                // JOIN FAVOURITES
                ->leftJoin('favourites', function ($join) use ($userId) {
                    $join->on('favourites.post_id', '=', 'posts.id')
                        ->where('favourites.user_id', '=', $userId);
                })

                ->select(
                    'posts.id',
                    'posts.user_id',
                    'posts.caption',
                    'posts.thumbnail_url',
                    'posts.total_like',
                    'posts.total_comment',
                    'posts.created_at',
                    'users.full_name as author_fullname',
                    'users.avatar_url as author_avatar',

                    // Has liked?
                    \DB::raw("IF(like_posts.user_id IS NULL, 0, 1) as isLiked"),

                    // Has favourited?
                    \DB::raw("IF(favourites.user_id IS NULL, 0, 1) as isSaved")
                )

                ->orderBy('posts.created_at', 'desc')
                ->get();

            return $this->responseApi->success($listPost);

        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Create a new post
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function addPost(Request $request)
    {
        try {
            $param = $request->all();
            $mediaUrl = null;

            // Kiểm tra file gửi lên (chấp nhận key là 'thumbnail' hoặc 'file')
            $file = $request->file('thumbnail') ?? $request->file('file');

            if ($file) {
                // Lấy tên gốc và tạo tên file mới để tránh trùng
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Đường dẫn lưu: public/uploads/posts
                $path = public_path('uploads/posts');

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                // Di chuyển file vào thư mục
                $file->move($path, $filename);

                // Tạo URL đầy đủ
                $mediaUrl = url('uploads/posts/' . $filename);
            }

            $createPost = Post::create([
                // Ưu tiên lấy ID từ Auth (người dùng đang đăng nhập), nếu không có thì lấy từ request
                'user_id' => \Auth::id() ?? $param['user_id'], 
                'caption' => $request->caption ? $request->caption : '',
                'thumbnail_url' => $mediaUrl, // Lưu URL của ảnh hoặc video
                'total_like' => 0,
                'total_comment' => 0
            ]);

            return $this->responseApi->success([
                'message' => __('message.post_saved_successfully'),
                'post'    => $createPost
            ]);

        } catch (\Throwable $th) {
            \Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Delete post by id
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
     * @throws \Throwable
     */
    public function deletePost(Request $request)
    {
        try {
            $post = Post::find($request->id);
            if (!$post) {
                return $this->responseApi->dataNotFound();
            }
            DB::beginTransaction();
            // Xóa file thumbnail nếu có
            if ($post->thumbnail_url) {
                $filePath = public_path(str_replace(url('/') . '/', '', $post->thumbnail_url));
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            // Xóa liên quan
            $post->comments()->delete();
            $post->likes()->delete();
            $post->favourites()->delete();
            $post->delete();

            DB::commit();
            return $this->responseApi->success([
                'message' => __('message.post_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Post Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Like/Unlike a post by user id and post id.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
     * @throws \Throwable
     */
    public function likePost(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->input('user_id');
            $postId = $request->input('post_id');

            if (!$userId) {
                return $this->responseApi->badRequest("Missing user_id");
            }

            $post = Post::find($postId);
            if (!$post) {
                return $this->responseApi->dataNotFound();
            }

            $like = LikePost::where('user_id', $userId)
                            ->where('post_id', $postId)
                            ->first();

            if ($like) {
                $like->delete();
                $post->total_like = max($post->total_like - 1, 0);
                $post->save();

                return $this->responseApi->success([
                    'message' => __('message.post_unliked_successfully'),
                    'total_like' => $post->total_like
                ]);
            }

            LikePost::create([
                'user_id' => $userId,
                'post_id' => $postId
            ]);

            $post->total_like += 1;
            $post->save();

            if ($userId != $post->user_id) {
                try {
                    $noti = Notification::create([
                        'user_id'  => $post->user_id,
                        'actor_id' => $userId,
                        'type'     => Notification::NOTI_LIKE_POST,
                        'post_id'  => $postId,
                        'content'  => 'đã thích bài viết của bạn.',
                        'is_view'  => Notification::IS_NOT_VIEWED,
                        'status'   => Notification::STATUS_WAIT
                    ]);

                    // Kích hoạt bắn ngay lập tức
                    NotificationController::sendPushNow($noti);
                } catch (\Throwable $th) {
                    Log::error('Error creating notification for like post: ' . $th->getMessage());
                }
            }

            return $this->responseApi->success([
                'message' => __('message.post_liked_successfully'),
                'total_like' => $post->total_like
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Save/Unsave a post by user id and post id.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
     * @throws \Throwable
     */
    public function savePost(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->input('user_id');
            $postId = $request->input('post_id');

            if (!$userId) {
                return $this->responseApi->badRequest("Missing user_id");
            }

            $post = Post::find($postId);
            if (!$post) {
                return $this->responseApi->dataNotFound();
            }

            $favourite = Favourite::where('user_id', $userId)
                ->where('post_id', $postId)
                ->first();

            if ($favourite) {
                $favourite->delete();

                return $this->responseApi->success([
                    'message' => __('message.post_removed_from_favourites'),
                    'saved' => false
                ]);
            }

            Favourite::create([
                'user_id' => $userId,
                'post_id' => $postId
            ]);

            return $this->responseApi->success([
                'message' => __('message.post_saved'),
                'saved' => true
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Get all posts by user id.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function myPost(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->user_id;
            if (!$userId) {
                return $this->responseApi->dataNotFound();
            }

            $listMyPost = DB::table('users')
                ->join('posts', 'users.id', '=', 'posts.user_id')
                ->where('users.id', $userId)
                ->select(
                    'posts.id',
                    'posts.user_id',
                    'posts.caption',
                    'posts.thumbnail_url',
                    'posts.total_like',
                    'posts.total_comment',
                    'posts.created_at',
                    'users.full_name as author_fullname',
                    'users.avatar_url as author_avatar'
                )
                ->orderByDesc('posts.created_at')
                ->get();

            return $this->responseApi->success($listMyPost);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Get all saved posts by user id.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function mySaved(Request $request)
    {
        try {
            $userId = Auth::id() ?? $request->user_id;
            if (!$userId) {
                return $this->responseApi->dataNotFound();
            }

            $listMySaved = DB::table('users')
                ->join('favourites', 'users.id', '=', 'favourites.user_id')
                ->join('posts', 'favourites.post_id', '=', 'posts.id')
                ->where('users.id', $userId)
                ->select(
                    'posts.id',
                    'posts.user_id',
                    'posts.caption',
                    'posts.thumbnail_url',
                    'posts.total_like',
                    'posts.total_comment',
                    'posts.created_at',
                    'users.full_name as author_fullname',
                    'users.avatar_url as author_avatar'
                )
                ->orderByDesc('posts.created_at')
                ->get();

            return $this->responseApi->success($listMySaved);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Explore all posts, sorted by total likes in descending order.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * 
     * @throws \Throwable
     */
    public function explorePost(Request $request)
    {
        try {
            $listPostEplore = Post::join('users', 'users.id', '=', 'posts.user_id')
                ->select(
                    'posts.id',
                    'posts.user_id',
                    'posts.caption',
                    'posts.thumbnail_url',
                    'posts.total_like',
                    'posts.total_comment',
                    'posts.created_at',
                    'users.full_name as author_fullname',
                    'users.avatar_url as author_avatar'
                )
                ->orderBy('posts.total_like', 'desc')
                ->get();

            return $this->responseApi->success($listPostEplore);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Comment on a post.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function comment(Request $request)
    {
        try {
            // Validate
            $request->validate([
                'post_id'   => 'required|integer|exists:posts,id',
                'comment'   => 'required|string',
                'parent_id' => 'nullable|integer|exists:comments,id'
            ]);

            // Lấy user id: ưu tiên Auth, nếu không có dùng input
            $userId = Auth::id() ?? $request->input('user_id');
            if (!$userId) {
                return $this->responseApi->badRequest('User not authenticated');
            }

            $postId   = $request->input('post_id');
            $comment  = $request->input('comment');
            $parentId = $request->input('parent_id') ?? null;

            \DB::beginTransaction();

            // Tạo comment
            $newComment = Comment::create([
                'user_id'   => $userId,
                'post_id'   => $postId,
                'comment'   => $comment,
                'parent_id' => $parentId
            ]);

            // Tăng total_comment trên posts (dùng increment cho an toàn)
            $post = Post::find($postId);
            if ($post) {
                $post->increment('total_comment', 1);

                // Chỉ gửi thông báo nếu người comment không phải là chủ bài viết
                if ($userId != $post->user_id) {
                    try {
                        $noti = Notification::create([
                            'user_id'  => $post->user_id, // Chủ bài viết nhận thông báo
                            'actor_id' => $userId,       // Người bình luận là actor,
                            'post_id'  => $postId,
                            'type'     => Notification::NOTI_COMMENT, // Giá trị = 2 (theo Model của bạn)
                            'content'  => 'đã bình luận về bài viết của bạn.',
                            'is_view'  => Notification::IS_NOT_VIEWED,
                            'status'   => Notification::STATUS_WAIT
                        ]);
                        // Kích hoạt bắn ngay lập tức
                        NotificationController::sendPushNow($noti);
                    } catch (\Throwable $th) {
                        Log::error('Error creating notification for comment: ' . $th->getMessage());
                    }
                    
                }
            }

            \DB::commit();

            // Load user relation để FE render (và children nếu cần)
            $newComment->load('user:id,full_name,user_name,avatar_url');

            return $this->responseApi->success([
                'comment' => $newComment,
                'total_comment' => $post ? $post->total_comment : null,
                'message' => __('message.comment_successfully')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }

    /**
     * Get all comments of a post with user information, sorted by created_at in descending order.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function listComment(Request $request)
    {
        try {
            $postId = $request->post_id;

            $comments = Comment::with([
                'user:id,full_name,user_name,avatar_url',
                'children.user:id,full_name,user_name,avatar_url'
            ])
                ->where('post_id', $postId)
                ->whereNull('parent_id')
                ->select('id', 'comment', 'parent_id', 'user_id', 'post_id', 'created_at')
                ->get();

            return $this->responseApi->success(compact('comments'));
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->responseApi->internalServerError();
        }
    }
}
