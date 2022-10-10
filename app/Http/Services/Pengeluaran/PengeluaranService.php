<?php
namespace App\Http\Services\Pengeluaran;

use Exception;
use App\Models\Akun;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Akun\AkunResource;

class PengeluaranService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pengeluaranModel;

    public function __construct()
    {
        $this->pengeluaranModel = new Akun();
    }

    /* FETCH ALL PENGELUARAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->pengeluaranModel, [], null);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->pengeluaranModel, $props, null);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->pengeluaranModel, $props, null);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = AkunResource::collection($datas);
        $pengeluaran = [
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

        return $pengeluaran;
    }

    /* FETCH PENGELUARAN BY ID */
    public function fetchById($id){
        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                $pengeluaran = AkunResource::make($pengeluaran);
                return $pengeluaran;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PENGELUARAN */
    public function createAkun($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pengeluaran = $this->pengeluaranModel;
            $pengeluaran->kode_akun    = $props['kode_akun'];
            $pengeluaran->nama_akun    = $props['nama_akun'];
            $pengeluaran->akun_utama   = $props['akun_utama'];
            $pengeluaran->tipe_akun    = $props['tipe_akun'];
            $pengeluaran->created_id   = $this->returnAuthUser()->id;
            $pengeluaran->save();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $pengeluaran = AkunResource::make($pengeluaran);
            return $pengeluaran;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE PENGELUARAN */
    public function updateAkun($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                /* UPDATE PENGELUARAN */
                $pengeluaran->kode_akun    = $props['kode_akun'];
                $pengeluaran->nama_akun    = $props['nama_akun'];
                $pengeluaran->akun_utama   = $props['akun_utama'];
                $pengeluaran->tipe_akun    = $props['tipe_akun'];
                $pengeluaran->updated_id   = $this->returnAuthUser()->id;
                $pengeluaran->update();

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $pengeluaran = AkunResource::make($pengeluaran);
                return $pengeluaran;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY PENGELUARAN */
    public function destroyAkun($id){
        try {
            $pengeluaran = $this->pengeluaranModel::find($id);
            if ($pengeluaran) {
                $pengeluaran->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PENGELUARAN */
    public function destroyMultipleAkun($props){
        try {
            $pengeluaran = $this->pengeluaranModel::whereIn('id', $props);

            if ($pengeluaran->count() > 0) {
                $pengeluaran->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PENGELUARAN FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->pengeluaranModel, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $pengeluaran = $datas->select('id', 'kode_akun', 'nama_akun')->get();

            return $pengeluaran;
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
