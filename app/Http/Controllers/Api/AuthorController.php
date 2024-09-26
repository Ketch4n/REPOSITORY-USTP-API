<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
            $result = Project::join('authors','projects.id','=','authors.project_id')
            ->select('authors.*','projects.title','projects.project_type','projects.year_published')
            ->get();

            return AuthorResource::collection($result);
            

    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[

            'group_name'=> 'required|string|max:255',
            'project_id'=> 'required|integer',
            'member_0' => 'nullable|string|max:255',
            'member_1' => 'nullable|string|max:255',
            'member_2' => 'nullable|string|max:255',
            'member_3' => 'nullable|string|max:255',
                
            ]);

        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'error'=>$validator->messages(),],422);
        }

        try 
        {
            $author = Author::create([
            'group_name'=> $request->group_name,
            'project_id'=> $request->project_id,
            'member_0'=> $request->member_0,
            'member_1'=> $request->member_1,
            'member_2'=> $request->member_2,
            'member_3'=> $request->member_3,
            ]);
        
            return response()->json([
                'message'=> 'AUTHOR ADDED',
                'data'=> new AuthorResource($author)
            ], 200);
        }
        
        catch (\Exception $e) {
             return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500); 
        }

    }
    public function update(Request $request, Author $author)
    {

        $validator = Validator::make($request->all(),[

            'group_name'=> 'required|string|max:255',
            'project_id'=> 'required|integer',
            'member_0' => 'nullable|string|max:255',
            'member_1' => 'nullable|string|max:255',
            'member_2' => 'nullable|string|max:255',
            'member_3' => 'nullable|string|max:255',
                
            ]);

        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'error'=>$validator->messages(),],422);
        }

        try 
        {
            $author->update([
            'group_name'=> $request->group_name,
            'project_id'=> $request->project_id,
            'member_0'=> $request->member_0,
            'member_1'=> $request->member_1,
            'member_2'=> $request->member_2,
            'member_3'=> $request->member_3,
            ]);
        
            return response()->json([
                'message'=> 'AUTHOR UPDATED',
                'data'=> new AuthorResource($author)
            ], 200);
        }
        
        catch (\Exception $e) {
             return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500); 
        }

    }
    public function destroy(Request $author){
        $author->delete();
        return response()->json([
            'message'=> 'AUTHOR DELETED',
            'quack' => true,
        ],200);
    }
}
