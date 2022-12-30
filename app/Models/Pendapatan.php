<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendapatan extends BaseModel
{
    use Notifiable, SoftDeletes;

    protected $searchable = [
        'columns' => [
            'tanggal' => 10,
            'grand_total' => 10,
            'gambar' => 5,
            'status' => 5,
        ]
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'tanggal', 'grand_total', 'gambar', 'status', 'id_user',
    ];

    protected $table = 'pendapatan_parkir';

    public function details()
    {
        return $this->hasMany('App\Models\DetailPendapatan', 'id_pendapatan_parkir', 'id');
    }
}
