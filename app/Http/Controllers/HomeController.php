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
use Madokami\Models\FileRecord;
use Madokami\Upload\FileUpload;
use Storage;

class HomeController extends Controller {

    protected $fileUpload;

    public function __construct(FileUpload $fileUpload) {
        $this->fileUpload = $fileUpload;
    }

    public function home() {

        return view('home');
    }

    public function upload(Request $request) {
        if(!$request->hasFile('file')) {
            throw new NoFileException();
        }

        $record = $this->fileUpload->uploadFile($request, 'file');

        return response()->json($record->toArray());

    }

}