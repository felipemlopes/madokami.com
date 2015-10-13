<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 09/09/15
 * Time: 23:04
 */

namespace Madokami\Http\Controllers;


use Illuminate\Http\Request;
use Madokami\Exceptions\NoFileException;
use Madokami\Formatters\FileSizeFormatter;
use Madokami\Models\FileRecord;
use Madokami\Upload\FileUpload;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HomeController extends Controller {

    /** @var FileUpload $fileUpload */
    protected $fileUpload;

    public function __construct(FileUpload $fileUpload) {
        $this->fileUpload = $fileUpload;
    }

    public function home() {

        $maxUploadSize = config('upload.max_size');
        $displayMaxUploadSize = FileSizeFormatter::format($maxUploadSize);

        return view('home')
            ->with('maxUploadSize', $maxUploadSize)
            ->with('displayMaxUploadSize', $displayMaxUploadSize);
    }

    public function upload(Request $request) {
        if($request->files->count() === 0) {
            throw new NoFileException();
        }
        else {
            $exported = [ ];

            /** @var UploadedFile $file */
            foreach($request->files as $file) {
                if($file->isValid()) {
                    $record = $this->fileUpload->uploadFile($file, $request->getClientIp());
                    $exported[] = $record->toArray();
                }
                else {
                    throw new UploadException();
                }
            }

            return response()->json([ 'success' => true, 'files' => $exported ]);
        }
    }

}