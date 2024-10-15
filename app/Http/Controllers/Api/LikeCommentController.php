<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\LikeCommentModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LikeCommentResource;

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

            'user_id'=> 'required|integer',
            'project_id'=> 'required|integer',
            'rating' => 'nullable|integer',
            'comment' => 'nullable|string|max:255',
        
                
            ]);

        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'error'=>$validator->messages(),],422);
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
                'data'=> new LikeCommentResource($comment)
            ], 200);
        }
        
        catch (\Exception $e) {
             return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500); 
        }

    }

    public function show(LikeCommentModel $comment)
    {
        return new LikeCommentResource($comment);
    }
    
    public function update(Request $request, LikeCommentModel $comment)
    {


        try 
        {
            $comment->update([
            'rating'=> $request->rating,
            'comment'=> $request->comment,
            ]);
        
            return response()->json([
                'message'=> 'UPDATED SUCCESSFULLY',
                'data'=> new LikeCommentResource($comment)
            ], 200);
        }
        
        catch (\Exception $e) {
             return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500); 
        }

    }

    public function destroy(Request $comment){
        $comment->delete();
        return response()->json([
            'message'=> 'COMMENT DELETED',
            'quack' => true,
        ],200);
    }
}
