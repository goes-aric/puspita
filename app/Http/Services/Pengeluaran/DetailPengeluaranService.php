<?php
namespace App\Http\Services\Pengeluaran;

use Exception;
use App\Models\DetailPengeluaran;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Pengeluaran\DetailPengeluaranResource;
use App\Models\Pengeluaran;

class DetailPengeluaranService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pengeluaranModel;
    private $detailModel;
    private $carbon;

    public function __construct()
    {
        $this->pengeluaranModel = new Pengeluaran();
        $this->detailModel = new DetailPengeluaran();
        $this->carbon = $this->returnCarbon();
    }

    /* FETCH ALL DETAIL PENGELUARAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->detailModel, [], null)->where('id_pengeluaran_parkir', '=', $props['id']);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->detailModel, $props, null)->where('id_pengeluaran_parkir', '=', $props['id']);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->detailModel, $props, null)->where('id_pengeluaran_parkir', '=', $props['id']);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = DetailPengeluaranResource::collection($datas);
        $detail = [
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

        return $detail;
    }

    /* FETCH DETAIL JURNAL BY ID */
    public function fetchById($id){
        try {
            $detail = $this->detailModel::find($id);
            if ($detail) {
                $detail = DetailPengeluaranResource::make($detail);
                return $detail;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW DETAIL PENGELUARAN */
    public function createDetail($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $detail = $this->detailModel;
            $detail->id_pengeluaran_parkir  = $props['id'];
            $detail->tanggal                = $props['tanggal'];
            $detail->kode_akun              = $props['kode_akun'];
            $detail->nama_akun              = $props['nama_akun'];
            $detail->jumlah_pengeluaran     = $props['jumlah_pengeluaran'];
            $detail->save();

            /* UPDATE JUMLAH TOTAL ON PENGELUARAN PARKIR */
            $this->updateJumlahTotal($props['id']);

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $detail = DetailPengeluaranResource::make($detail);
            return $detail;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE DETAIL PENGELUARAN */
    public function updateDetail($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $detail = $this->detailModel::find($id);
            if ($detail) {
                /* UPDATE DETAIL PENGELUARAN */
                $detail->id_pengeluaran_parkir  = $props['id'];
                $detail->tanggal                = $props['tanggal'];
                $detail->kode_akun              = $props['kode_akun'];
                $detail->nama_akun              = $props['nama_akun'];
                $detail->jumlah_pengeluaran     = $props['jumlah_pengeluaran'];
                $detail->update();

                /* UPDATE JUMLAH TOTAL ON PENGELUARAN PARKIR */
                $this->updateJumlahTotal($props['id']);

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $detail = DetailPengeluaranResource::make($detail);
                return $detail;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* DESTROY DETAIL PENGELUARAN */
    public function destroyDetail($id){
        try {
            $detail = $this->detailModel::find($id);
            if ($detail) {
                /* UPDATE JUMLAH TOTAL ON PENGELUARAN PARKIR */
                $this->updateJumlahTotal($detail['id_pengeluaran_parkir']);
                $detail->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE DETAIL PENGELUARAN */
    public function destroyMultipleDetail($props){
        try {
            $detail = $this->detailModel::whereIn('id', $props);

            if ($detail->count() > 0) {
                /* UPDATE JUMLAH TOTAL ON PENGELUARAN PARKIR */
                $this->updateJumlahTotal($detail['id_pengeluaran_parkir']);
                $detail->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function updateJumlahTotal($id){
        $detail = $this->detailModel::select(DB::raw('SUM(jumlah_pengeluaran) AS total'))->where('id_pengeluaran_parkir', '=', $id)->get();
        $pengeluaran = $this->pengeluaranModel::find($id);
        $pengeluaran->grand_total = $detail[0]['total'];
        $pengeluaran->update();
    }
}
