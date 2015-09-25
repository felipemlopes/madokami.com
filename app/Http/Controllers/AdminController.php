<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 25/09/15
 * Time: 22:44
 */

namespace Madokami\Http\Controllers;


use Illuminate\Http\Request;
use Madokami\Filters\Filters;
use Madokami\Models\FileRecord;

class AdminController extends Controller {

    public function home(Request $request) {
        $filters = new Filters($request->get('filters'));

        $query = FileRecord::query()
            ->filter($filters);

        $count = $query->count();

        $files = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($filters->queryParameters());


        return view('admin.home')
            ->with('count', $count)
            ->with('files', $files)
            ->with('filters', $filters);
    }

}