<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FileController extends Controller
{
   public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads', 'bucket');

        $file = File::create([
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploadedFile->getClientMimeType(),
            'size' => $uploadedFile->getSize(),
        ]);

        $url = Storage::disk('bucket')->url($path);

        $data = [
            'id' => $file->id,
            'original_name' => $file->original_name,
            'url' => $url,
            'size' => $file->size,

        ];

        return $this->responseData($data, notification()->success('File created', 'File created successfully'));
    }
}