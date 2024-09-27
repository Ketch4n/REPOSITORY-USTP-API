<?php

namespace App\Http\Controllers\api;

use App\Models\ViewedModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ViewedResource;
use Illuminate\Support\Facades\Validator;


class ViewedController extends Controller
{
    
    public function index(){

        return new ViewedResource($viewed);
    }
    public function show(ViewedModel $viewed){
        
    }
    public function store(Request $request){
        
        $validator = Validator::make($request->all(),[
            'project_id'=>'required|integer|max:11',
            'user_id'=> 'required|integer|max:11',
            'file_name'=> 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message'=> 'ALL FIELDS REQUIRED',
                'quack'=> false,
            ],422);
        }

        $viewed = ViewedModel::create([
            'project_id'=> $request->project_id,
            'user_id'=> $request->user_id,
            'file_name'=> $request->file_name,
        ]);

        return response()->json([
            'message'=> 'RECORDED SUCCESSFULLY',
            'quack'=> true,
            'data'=> new ViewedResource($viewed)
        ],200);
    }   
    
    
}