<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends BaseModel
{
    use Notifiable, SoftDeletes;

    protected $searchable = [
        'columns' => [
            'jenis_kendaraan' => 10,
            'biaya_parkir' => 10,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'jenis_kendaraan', 'biaya_parkir', 'id_user',
    ];

    protected $table = 'kendaraan';
}
