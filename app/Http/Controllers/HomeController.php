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

class HomeController extends Controller {

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
        if(!$request->hasFile('file')) {
            throw new NoFileException();
        }

        $record = $this->fileUpload->uploadFile($request, 'file');

        return response()->json($record->toArray());

    }

}