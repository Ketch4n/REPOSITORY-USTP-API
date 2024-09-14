<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

class FirebaseController extends Controller
{
    protected $storage;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
        $this->storage = $factory->createStorage();
    }

    public function uploadFile($filePath)
    {
        $bucket = $this->storage->getBucket();
        $file = fopen($filePath, 'r');
        $bucket->upload($file, [
            'name' => basename($filePath)
        ]);

        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
