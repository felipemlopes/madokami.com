<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 19/10/2015
 * Time: 21:17
 */

namespace Madokami\VirusTotal;

use Guzzle\Http\Exception\ServerErrorResponseException;
use VirusTotal\File as BaseFile;

class File extends BaseFile {

    public function privateUploadUrl() {
        $data = $this->makeGetRequest('file/scan/upload_url', array(
            'apikey' => $this->_apiKey,
        ));

        return $data->json();
    }

    public function privateScan($privateUrl, $file) {
        $data = $this->_client->post($privateUrl)
            ->addPostFiles(array('file' => $file));

        try {
            $response = $data->send();
        }
        catch(ServerErrorResponseException $exception) {
            dd($exception->getRequest()->__toString(), $exception->getResponse()->getBody(true));
        }

        return $data->send()->json();
    }

}