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
use Storage;

class HomeController extends Controller {

    public function home() {

        return view('home');
    }

    public function upload(Request $request) {
        if(!$request->hasFile('file')) {
            throw new NoFileException();
        }

        $file = $request->file('file');

        $record = new FileRecord();

        $record->client_name = $file->getClientOriginalName();

        $randMin = base_convert('100000', 36, 10);
        $randMax = base_convert('zzzzzz', 36, 10);
        $generatedBaseName = base_convert(mt_rand($randMin, $randMax), 10, 36);
        $generatedName = $generatedBaseName;

        $ext = $file->getClientOriginalExtension();

        if($ext) {
            $generatedName .= '.'.strtolower($ext);
        }

        $record->generated_name = $generatedName;
        $record->filesize = $file->getSize();
        $record->hash = hash_file('sha256', $file->getPathname());
        $record->uploaded_by_ip = $request->getClientIp();

        Storage::move($file->getPathname(), $record->generated_name);

        $record->save();

        return response()->json($record->toArray());

    }

}