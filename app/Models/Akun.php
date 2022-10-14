<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Akun extends BaseModel
{
    use Notifiable, SoftDeletes;

    protected $searchable = [
        'columns' => [
            'kode_akun' => 10,
            'nama_akun' => 10,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'kode_akun', 'nama_akun', 'id_user',
    ];

    protected $table = 'akun';
}
