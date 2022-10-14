<?php
namespace App\Http\Resources\Akun;

use Illuminate\Http\Resources\Json\JsonResource;

class AkunResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'kode_akun'     => $this->kode_akun,
            'nama_akun'     => $this->nama_akun,
            'created_user'  => $this->createdUser->nama_user,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
