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
use Madokami\Exceptions\MaxUploadSizeException;
use Madokami\Exceptions\NoUniqueGeneratedNameException;
use Madokami\Models\FileRecord;
use Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload {

    /**
     * Upload provided file and create matching FileRecord object
     *
     * @param UploadedFile $file
     * @param $clientIp
     * @return FileRecord
     */
    public function uploadFile(UploadedFile $file, $clientIp) {
        $extension = Str::lower($file->getClientOriginalExtension());
        $generatedName = $this->generateName($extension);

        // Create SHA-256 hash of file
        $fileHash = hash_file('sha256', $file->getPathname());

        // Get filesize
        $filesize = $file->getSize();

        // Check max upload size
        $maxUploadSize = config('upload.max_size');
        if($filesize > $maxUploadSize) {
            throw new MaxUploadSizeException();
        }

        // Move the file
        $uploadDirectory = config('upload.directory');
        $file->move($uploadDirectory, $generatedName);

        // Create the record
        /** @var FileRecord $record */
        $record = FileRecord::create([
            'client_name' => $file->getClientOriginalName(),
            'generated_name' => $generatedName,
            'filesize' => $filesize,
            'hash' => $fileHash,
            'uploaded_by_ip' => $clientIp,
        ]);

        return $record;
    }

    /**
     * Generate a unique name for file
     *
     * @param $extension
     * @return string
     */
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

        // 10 tries and still no unique name, throw exception
        throw new NoUniqueGeneratedNameException();
    }

}