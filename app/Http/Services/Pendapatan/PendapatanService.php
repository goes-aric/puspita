<?php
namespace App\Http\Services\Pendapatan;

use Exception;
use App\Models\Akun;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Akun\AkunResource;

class PendapatanService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pendapatanModel;

    public function __construct()
    {
        $this->pendapatanModel = new Akun();
    }

    /* FETCH ALL PENDAPATAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->pendapatanModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->pendapatanModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->pendapatanModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = AkunResource::collection($datas);
        $pendapatan = [
            "total" => $totalData,
            "total_filter" => $totalFiltered,
            "per_page" => $props['take'],
            "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
            "last_page" => ceil($totalFiltered / $props['take']),
            "from" => $totalFiltered === 0 ? 0 : ($props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1),
            "to" => $totalFiltered === 0 ? 0 : ($props['skip'] * $props['take']) + $datas->count(),
            "show" => [
                ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"]
            ],
            "data" => $datas
        ];

        return $pendapatan;
    }

    /* FETCH PENDAPATAN BY ID */
    public function fetchById($id){
        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                $pendapatan = AkunResource::make($pendapatan);
                return $pendapatan;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PENDAPATAN */
    public function createAkun($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pendapatan = $this->pendapatanModel;
            $pendapatan->kode_akun    = $props['kode_akun'];
            $pendapatan->nama_akun    = $props['nama_akun'];
            $pendapatan->akun_utama   = $props['akun_utama'];
            $pendapatan->tipe_akun    = $props['tipe_akun'];
            $pendapatan->created_id   = $this->returnAuthUser()->id;
            $pendapatan->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $pendapatan = AkunResource::make($pendapatan);
            return $pendapatan;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE PENDAPATAN */
    public function updateAkun($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                /* UPDATE PENDAPATAN */
                $pendapatan->kode_akun    = $props['kode_akun'];
                $pendapatan->nama_akun    = $props['nama_akun'];
                $pendapatan->akun_utama   = $props['akun_utama'];
                $pendapatan->tipe_akun    = $props['tipe_akun'];
                $pendapatan->updated_id   = $this->returnAuthUser()->id;
                $pendapatan->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $pendapatan = AkunResource::make($pendapatan);
                return $pendapatan;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY PENDAPATAN */
    public function destroyAkun($id){
        try {
            $pendapatan = $this->pendapatanModel::find($id);
            if ($pendapatan) {
                $pendapatan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PENDAPATAN */
    public function destroyMultipleAkun($props){
        try {
            $pendapatan = $this->pendapatanModel::whereIn('id', $props);

            if ($pendapatan->count() > 0) {
                $pendapatan->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PENDAPATAN FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->pendapatanModel, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $pendapatan = $datas->select('id', 'kode_akun', 'nama_akun')->get();

            return $pendapatan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* GENERATE NO TRANSAKSI AUTOMATICALLY */
    public function createNoTransaksi(){
        $year   = $this->carbon::now()->format('Y');

        $newID  = "";
        $maxID  = DB::select('SELECT IFNULL(RIGHT(MAX(kode_user), 5), 0) AS maxID FROM users WHERE deleted_at IS NULL AND RIGHT(LEFT(kode_user, 7), 4) = :id', ['id' => $year]);
        $newID  = (int)$maxID[0]->maxID + 1;
        $newID  = 'PJ-'.$year.''.substr("0000000$newID", -3);

        return $newID;
    }
}
