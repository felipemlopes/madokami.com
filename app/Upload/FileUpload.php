<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 17/09/15
 * Time: 20:56
 */

namespace Madokami\Upload;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Madokami\Models\FileRecord;
use Storage;

class FileUpload {

    public function uploadFile(Request $request, $fileKey) {
        $file = $request->file($fileKey);

        $extension = Str::lower($file->getClientOriginalExtension());
        $generatedName = $this->generateName($extension);

        // Create SHA-256 hash of file
        $fileHash = hash_file('sha256', $file->getPathname());

        // Move the file
        $uploadDirectory = config('upload.directory');
        $file->move($uploadDirectory, $generatedName);

        // Create the record
        $record = FileRecord::create([
            'client_name' => $file->getClientOriginalName(),
            'generated_name' => $generatedName,
            'filesize' => $file->getSize(),
            'hash' => $fileHash,
            'uploaded_by_ip' => $request->getClientIp(),
        ]);

        return $record;
    }

    protected function generateName($extension) {
        for($i = 0; $i < 10; $i++) {
            // 6 chars long
            $randMin = base_convert('100000', 36, 10);
            $randMax = base_convert('zzzzzz', 36, 10);

            // Generate
            $generatedName = base_convert(mt_rand($randMin, $randMax), 10, 36);

            // Add extension if we have it
            if (!empty($extension)) {
                $generatedName .= '.' . $extension;
            }

            // Check name is unique
            $count = FileRecord::where('generated_name', '=', $generatedName)->count();
            if ($count === 0) {
                // Unique, return the name
                return $generatedName;
            }
            else {
                // Try again
                continue;
            }
        }
    }

}