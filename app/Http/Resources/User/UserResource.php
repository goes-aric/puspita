<?php
namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'nama_user' => ucwords(strtolower($this->nama_user)),
            'alamat'    => $this->alamat,
            'jabatan'   => $this->jabatan,
            'email'     => $this->email,
            'username'  => $this->username
        ];
    }
}
