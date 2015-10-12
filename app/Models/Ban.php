<?php

namespace Madokami\Models;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'ip', 'file_record_id' ];

    public static function createFromFileRecord(FileRecord $fileRecord) {
        $ban = Ban::create([
            'ip' => $fileRecord->uploaded_by_ip,
            'file_record_id' => $fileRecord->id,
        ]);

        return $ban;
    }

}
