<?php

namespace twa\smsautils\Http\Controllers;

use twa\smsautils\Models\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use twa\apiutils\Traits\APITrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    use APITrait;
    public function upload(Request $request)
    {
        // Validate using Laravel's request validator
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);
    
        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }
    
        $uploadedFile = $request->file('file');
    
        // Store file in the configured "bucket" disk
        $path = $uploadedFile->store('uploads', 'bucket');
    
        $file = File::create([
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $uploadedFile->getClientMimeType(),
            'size'          => $uploadedFile->getSize(),
        ]);
    
        $url = Storage::disk('bucket')->url($path);
    
        $data = [
            'id'            => $file->id,
            'original_name' => $file->original_name,
            'url'           => $url,
            'size'          => $file->size,
        ];
    
        return $this->responseData(
            $data,
            notification()->success('File created', 'File created successfully')
        );
    }
}