<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Traits\S3Storage;


class TravelController extends Controller
{
    use S3Storage;
    public function store(TravelRequest $request)
    {
        if (count($request->files) > 0) {
            foreach ($request->file('files') as $file) {
                $type = typeFile($file->getClientMimeType());
                $fileUrl = $this->uploadFile($file, config('filesystems.imageUpload'));
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $type;
                $files[] = [
                    'name' => $fileName,
                    'path' => $fileUrl
                ];
            }
        }

        $travel = Travel::create($request->validated());
        if ($files) {
            $travel->files()->createMany($files);
        }
        return new TravelResource($travel);
    }
    public function update(Travel $travels, TravelRequest $request) {
        $travels->update($request->validated());
        return new TravelResource($travels);
    }
    
}
