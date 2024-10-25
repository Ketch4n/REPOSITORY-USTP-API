<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\CollectionModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
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
    public function update(Request $request, CollectionModel $collection)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'filetype' => 'required|integer',
            'file' => 'required|string',
        ]);

        try {
            // Update the appropriate field based on filetype
            switch ($validatedData['filetype']) {
                case 1:
                    $collection->update(['manuscript' => $validatedData['file']]);
                    break;
                case 2:
                    $collection->update(['poster' => $validatedData['file']]);
                    break;
                case 3:
                    $collection->update(['video' => $validatedData['file']]);
                    break;
                case 4: // Assuming this is for ZIP files
                    $collection->update(['zip' => $validatedData['file']]);
                    break;
                default:
                    return response()->json([
                        'quack' => false,
                        'message' => 'Invalid file type',
                    ], 400); // Return a 400 Bad Request for invalid file types
            }

            return response()->json([
                'quack' => true,
                'message' => 'Updated successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'quack' => false,
                'message' => 'Update failed',
                // Optionally include the error message for debugging
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateFileNULL(Request $request, $id)
    {
        $collection = CollectionModel::find($id);

        if (!$collection) {
            return response()->json([
                'quack' => false,
                'message' => 'Collection not found',
            ], 404);
        }

      
        $validator = Validator::make($request->all(), [
            'filetype' => 'required|integer',
            'file' => 'nullable', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'quack' => false,
            ], 422); 
        }

        try {
          
            $filetype = $validator->validated()['filetype'];

          
            switch ($filetype) {
                case 1:
                    $collection->update([
                        'manuscript' => $request->file, 
                    ]);
                    break;
                case 2:
                    $collection->update([
                        'poster' => $request->file,
                    ]);
                    break;
                case 3:
                    $collection->update([
                        'video' => $request->file,
                    ]);
                    break;
                case 4: 
                    $collection->update([
                        'zip' => $request->file,
                    ]);
                    break;
                default:
                    return response()->json([
                        'quack' => false,
                        'message' => 'Invalid file type',
                    ], 400); 
            }

            return response()->json([
                'quack' => true,
                'message' => 'Updated successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'quack' => false,
                'message' => 'Update failed',
              
            ], 500);
        }
    }


    
}
