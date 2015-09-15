<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 09/09/15
 * Time: 23:04
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class HomeController extends Controller {

    public function home() {
        return view('home');
    }

    public function upload(Request $request) {
        dd($request);
    }

}