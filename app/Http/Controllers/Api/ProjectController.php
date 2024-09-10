<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Validator;


class ProjectController extends Controller
{
    public function index()
    {
        $project = Project::get();
        if ($project->count() > 0)
        {
            
             $project = Project::join('authors','projects.id','=','authors.project_id')
            ->select('authors.group_name','projects.*')
            ->get();

            return ProjectResource::collection($project);
        }
        else
        {
            return response()->json(['message'=>'NO PROJECT DATA'],200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title'=> 'required|string|max:255',
            'project_type'=> 'required|integer|max:11',
            'privacy'=> 'required|integer',
            'year_published'=> 'required',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'quack'=> false,
                'error'=>$validator->messages(),],422);
                
        }

        $project = Project::create([
            'title'=> $request->title,
            'project_type'=> $request->project_type,
            'privacy'=> $request->privacy,
            'year_published'=> $request->year_published,
        ]);

        // $authorController = new AuthorController();
        // return $authorController->store($request);

        return response()->json([
            'message'=> 'PROJECT ADDED',
            'quack'=> true,
            'data'=> new ProjectResource($project)
        ],200);
    }

    public function show(Project $project)
    {
        return new ProjectResource($project);
    }
    
    public function update(Request $request, Project $project)
    {
         $validator = Validator::make($request->all(),[
            'title'=> 'required|string|max:255',
            'project_type'=> 'required|integer|max:11',
            'year_published'=> 'required',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'error'=>$validator->messages(),],422);
        }

        $project->update([
            'title'=> $request->title,
            'project_type'=> $request->project_type,
            'year_published'=> $request->year_published,
        ]);

        return response()->json([
            'message'=> 'PROJECT UPDATED',
            'data'=> new ProjectResource($project)
        ],200);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'message'=> 'PROJECT DELETED',
        ],200);
    }
}
