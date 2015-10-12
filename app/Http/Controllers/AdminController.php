<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 25/09/15
 * Time: 22:44
 */

namespace Madokami\Http\Controllers;


use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Madokami\Filters\Filters;
use Madokami\Formatters\FileSizeFormatter;
use Madokami\Models\Ban;
use Madokami\Models\FileRecord;
use DB;

class AdminController extends Controller {

    protected $db;

    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    public function home(Request $request) {
        $filters = new Filters($request->get('filters'));

        if($request->has('delete_and_ban')) {
            $files = FileRecord::ip($filters->ip)->get();

            foreach($files as $file) {
                $file->delete();
            }

            Ban::createFromIp($filters->ip);

            return redirect()->to($filters->url([ ], [ 'delete_and_ban' ]))
                ->with('success', new MessageBag([ 'Files deleted and IP ban created' ]));
        }

        $query = FileRecord::query()
            ->filter($filters);

        $count = $query->count();

        $files = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($filters->queryParameters());

        $size = intval($this->db->table('file_records')->sum('filesize'));
        $size = FileSizeFormatter::format($size);

        return view('admin.home')
            ->with('count', $count)
            ->with('size', $size)
            ->with('files', $files)
            ->with('filters', $filters);
    }

    public function post(Request $request) {
        if($request->has('delete')) {
            $fileId = $request->get('delete');
            $file = FileRecord::findOrFail($fileId);
            $file->delete();

            return redirect()->back()
                ->with('success', new MessageBag([ 'File deleted' ]));
        }
    }

}