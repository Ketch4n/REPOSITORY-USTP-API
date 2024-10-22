<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\CollectionModel;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;

class CollectionController extends Controller
{

    public function store(Request $request){
        
        $validator = Validator::make($request->all(),[
            'project_id' => 'required|integer|max:11',
            'manuscript' => 'nullable|string|max:255',
            'poster' => 'nullable|string|max:255',
            'video' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',

        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message'=>'ALL FIELDS ARE REQUIRED',
                'quack'=> false,
                // 'status'=>$validator->messages(),
            ],422);
                
        }

        $collection = CollectionModel::create([
            'project_id'=> $request->project_id,
            'manuscript'=> $request->manuscript,
            'poster'=> $request->poster,
            'video'=> $request->video,
            'zip'=> $request->zip,

        ]);

        return response()->json([
            'message'=> 'COLLECTION ADDED SUCCESSFULLY',
            'quack'=> true,
            'data'=> new CollectionResource($collection)
        ],200);
    }

    
}
