<?php
namespace App\Http\Services\Pendapatan;

use Exception;
use App\Models\DetailPendapatan;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Pendapatan\DetailPendapatanResource;
use App\Models\Pendapatan;

class DetailPendapatanService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pendapatanModel;
    private $detailModel;
    private $carbon;

    public function __construct()
    {
        $this->pendapatanModel = new Pendapatan();
        $this->detailModel = new DetailPendapatan();
        $this->carbon = $this->returnCarbon();
    }

    /* FETCH ALL DETAIL PENDAPATAN */
    public function fetchLimit($props){
        /* GET DATA FOR PAGINATION AS A MODEL */
        $getAllData = $this->dataFilterPagination($this->detailModel, [], null)->where('id_pendapatan_parkir', '=', $props['id']);
        $totalData = $getAllData->count();

        /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
        $getFilterData = $this->dataFilterPagination($this->detailModel, $props, null)->where('id_pendapatan_parkir', '=', $props['id']);
        $totalFiltered = $getFilterData->count();

        /* GET DATA WITH FILTER AS A MODEL */
        $datas = $this->dataFilter($this->detailModel, $props, null)->where('id_pendapatan_parkir', '=', $props['id']);

        /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
        $datas = $datas->get();
        $datas = DetailPendapatanResource::collection($datas);
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
                $detail = DetailPendapatanResource::make($detail);
                return $detail;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW DETAIL PENDAPATAN */
    public function createDetail($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $detail = $this->detailModel;
            $detail->id_pendapatan_parkir   = $props['id'];
            $detail->id_kendaraan           = $props['id_kendaraan'];
            $detail->jenis_kendaraan        = $props['jenis_kendaraan'];
            $detail->jumlah_kendaraan       = $props['jumlah_kendaraan'];
            $detail->biaya_parkir           = $props['biaya_parkir'];
            $detail->total                  = $props['total'];
            $detail->save();

            /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
            $this->updateJumlahTotal($props['id']);

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $detail = DetailPendapatanResource::make($detail);
            return $detail;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            throw $ex;
        }
    }

    /* UPDATE DETAIL PENDAPATAN */
    public function updateDetail($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $detail = $this->detailModel::find($id);
            if ($detail) {
                /* UPDATE DETAIL PENDAPATAN */
                $detail->id_pendapatan_parkir   = $props['id'];
                $detail->id_kendaraan           = $props['id_kendaraan'];
                $detail->jenis_kendaraan        = $props['jenis_kendaraan'];
                $detail->jumlah_kendaraan       = $props['jumlah_kendaraan'];
                $detail->biaya_parkir           = $props['biaya_parkir'];
                $detail->total                  = $props['total'];
                $detail->update();

                /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
                $this->updateJumlahTotal($props['id']);

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $detail = DetailPendapatanResource::make($detail);
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

    /* DESTROY DETAIL PENDAPATAN */
    public function destroyDetail($id){
        try {
            $detail = $this->detailModel::find($id);
            if ($detail) {
                /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
                $this->updateJumlahTotal($detail['id_pendapatan_parkir']);
                $detail->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE DETAIL PENDAPATAN */
    public function destroyMultipleDetail($props){
        try {
            $detail = $this->detailModel::whereIn('id', $props);

            if ($detail->count() > 0) {
                /* UPDATE JUMLAH TOTAL ON PENDAPATAN PARKIR */
                $this->updateJumlahTotal($detail['id_pendapatan_parkir']);
                $detail->delete();

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function updateJumlahTotal($id){
        $detail = $this->detailModel::select(DB::raw('SUM(total) AS total'))->where('id_pendapatan_parkir', '=', $id)->get();
        $pendapatan = $this->pendapatanModel::find($id);
        $pendapatan->grand_total = $detail[0]['total'];
        $pendapatan->update();
    }
}
