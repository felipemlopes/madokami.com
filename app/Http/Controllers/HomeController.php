<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 09/09/15
 * Time: 23:04
 */

namespace Madokami\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Madokami\Exceptions\DisallowedFileTypeException;
use Madokami\Exceptions\NoFileException;
use Madokami\Formatters\FileSizeFormatter;
use Madokami\Models\FileRecord;
use Madokami\Upload\FileUpload;
use Madokami\VirusTotal\Api\Client;
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

        //$client = new Client(config('virustotal.api_key'));
        //dd($client->report('51ca34d11b96b978799ff026302b7d2eeea3e05d710cde0e76a8b691050e6426'));

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
                    // Don't allow .exe files
                    if(strcasecmp($file->getClientOriginalExtension(), 'exe') === 0) {
                        throw new DisallowedFileTypeException();
                    }

                    $record = $this->fileUpload->uploadFile($file, $request->getClientIp());
                    $exported[] = $record->toArray();
                }
                else {
                    throw new UploadException();
                }
            }

            $headers = [
                'X-File-Url' => $exported[0]['url'],
            ];

            return response()->json([ 'success' => true, 'files' => $exported ], 200, $headers);
        }
    }

}