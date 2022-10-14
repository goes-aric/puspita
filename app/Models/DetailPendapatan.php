<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;

class DetailPendapatan extends BaseModel
{
    use Notifiable;

    protected $searchable = [
        'columns' => [
            'id_pendapatan_parkir' => 10,
            'id_kendaraan' => 10,
            'jenis_kendaraan' => 10,
            'jumlah_kendaraan' => 10,
            'biaya_parkir' => 10,
            'total' => 10,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'id_pendapatan_parkir', 'id_kendaraan', 'jenis_kendaraan', 'jumlah_kendaraan', 'biaya_parkir', 'total', 'id_user',
    ];

    protected $table = 'detail_pendapatan_parkir';
}
