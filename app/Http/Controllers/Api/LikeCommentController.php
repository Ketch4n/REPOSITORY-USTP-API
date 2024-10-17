<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\LikeCommentModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LikeCommentResource;
use App\Http\Controllers\Api\LikeCommentController;

class LikeCommentController extends Controller
{
    public function projectRatingComment(Request $request)
    {
        $projectId = $request->input('project_id');
    
        $comments = LikeCommentModel::join('users', 'like_comment.user_id', '=', 'users.id')
            ->join('projects', 'like_comment.project_id', '=', 'projects.id')
            ->where('like_comment.project_id', $projectId) 
            ->select([
                'like_comment.*',
                'users.username',
                'users.email',
            ])
            ->get();
    
        if ($comments->isNotEmpty()) {
            return LikeCommentResource::collection($comments);
        } else {
            return response()->json([
                'message' => 'NO DATA',
                'data' => [],
            ], 200);
        }

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[

            'user_id'=> 'required',
            'project_id'=> 'required',
            'rating' => 'required',
            'comment' => 'required',
        
                
            ]);

        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'quack'=> false,
                // 'message'=>$validator->messages(),
            ],422);
        }

        try 
        {
            $comment = LikeCommentModel::create([
            'user_id'=> $request->user_id,
            'project_id'=> $request->project_id,
            'rating'=> $request->rating,
            'comment'=> $request->comment,
            ]);
        
            return response()->json([
                'message'=> 'ADDED SUCCESSFULLY',
                'quack'=> true,
                // 'data'=> new LikeCommentResource($comment)
            ], 200);
        }
        
        catch (\Exception $e) {
             return response()->json([
                'quack' => false,
                'message' =>  $e->getMessage()
            ], 500); 
        }

    }

    public function show(LikeCommentModel $comment)
    {
        return new LikeCommentResource($comment);
    }
    
    public function updateRatingComment(Request $request, $id)
    {
        // Find the comment by ID
        $comment = LikeCommentModel::find($id);

        if (!$comment) {
            return response()->json([
                'quack' => false,
                'message' => 'Comment not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'ALL FIELDS ARE REQUIRED',
                'quack' => false,
            ], 422);
        }

        try {
            $comment->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $comment->refresh();
            return response()->json([
                'quack' => true,
                'message' => 'Updated successfully',
                'data' => $comment,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Update failed: ' . $e->getMessage());
            return response()->json([
                'quack' => false,
                'message' => 'Update failed',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    

    public function destroy($id) {
        // Find the comment by its ID
        $comment = LikeCommentModel::find($id);
    
        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'quack' => false,
            ], 404);
        }
    
        // Delete the comment
        $comment->delete();
    
        return response()->json([
            'message' => 'COMMENT AND RATING DELETED',
            'quack' => true,
        ], 200);
    }
    
}
