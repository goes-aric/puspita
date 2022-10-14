<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends BaseModel
{
    use Notifiable, SoftDeletes;

    protected $searchable = [
        'columns' => [
            'bulan' => 10,
            'tahun' => 10,
            'grand_total' => 5,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'bulan', 'tahun', 'grand_total', 'id_user',
    ];

    protected $table = 'pengeluaran_parkir';

    public function details()
    {
        return $this->hasMany('App\Models\DetailPengeluaran', 'id_pengeluaran_parkir', 'id');
    }
}
