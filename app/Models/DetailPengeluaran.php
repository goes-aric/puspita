<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;

class DetailPengeluaran extends BaseModel
{
    use Notifiable;

    protected $searchable = [
        'columns' => [
            'id_pengeluaran_parkir' => 10,
            'tanggal' => 10,
            'kode_akun' => 10,
            'nama_akun' => 10,
            'jumlah_pengeluaran' => 10,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'id_pengeluaran_parkir', 'tanggal', 'kode_akun', 'nama_akun', 'jumlah_pengeluaran', 'id_user',
    ];

    protected $table = 'detail_pengeluaran_parkir';
}
