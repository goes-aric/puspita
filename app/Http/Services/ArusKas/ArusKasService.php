<?php
namespace App\Http\Services\ArusKas;

use Exception;
use Illuminate\Support\Str;
use App\Models\Pendapatan;
use App\Models\DetailPendapatan;
use App\Models\Pengeluaran;
use App\Models\DetailPengeluaran;
use App\Http\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Pendapatan\DetailPendapatanService;

class ArusKasService extends BaseService
{
    /* PRIVATE VARIABLE */
    private $pendapatanModel;
    private $detailPendapatanModel;
    private $pengeluaranModel;
    private $detailPengeluaranModel;
    private $detailService;
    private $carbon;

    public function __construct()
    {
        $this->pendapatanModel = new Pendapatan();
        $this->detailPendapatanModel = new DetailPendapatan();
        $this->pendapatanModel = new Pendapatan();
        $this->detailPendapatanModel = new DetailPendapatan();
        $this->detailService = new DetailPendapatanService;
        $this->carbon = $this->returnCarbon();
    }

    /* FETCH LAPORAN PENDAPATAN */
    public function fetchPendapatan($props){
        $tanggalMulai = isset($props['start']) ? $props['start'] : $this->carbon::now();
        $tanggalAkhir = isset($props['end']) ? $props['end'] : $this->carbon::now();

        /* TRANSAKSI PER PERIODE */
        $datas = DB::table('detail_pendapatan_parkir')
                    ->join('pendapatan_parkir', 'detail_pendapatan_parkir.id_pendapatan_parkir', '=', 'pendapatan_parkir.id')
                    ->select(DB::raw("'1' AS kode, 'Pendapatan Uang Parkir' AS keterangan, IFNULL(SUM(total), 0) AS saldo"))
                    ->where('pendapatan_parkir.tanggal', '>=', $tanggalMulai)
                    ->where('pendapatan_parkir.tanggal', '<=', $tanggalAkhir)
                    ->get();

        /* SALDO AWAL */
        $saldoAwal = DB::table('detail_pendapatan_parkir')
                    ->join('pendapatan_parkir', 'detail_pendapatan_parkir.id_pendapatan_parkir', '=', 'pendapatan_parkir.id')
                    ->select(DB::raw("'1' AS kode, 'Pendapatan Uang Parkir' AS keterangan, IFNULL(SUM(total), 0) AS saldo"))
                    ->where('pendapatan_parkir.tanggal', '<', $tanggalMulai)
                    ->get();

        $arusKasPendapatan = [
            [
                'id'        => 'pendapatan',
                'nama'      => 'Pendapatan dari Aktivitas Operasional',
                'transaksi' => $datas,
                'saldo_awal'=> $saldoAwal
            ]
        ];
        return $arusKasPendapatan;
    }

    /* FETCH LAPORAN PENGELUARAN */
    public function fetchPengeluaran($props){
        $tanggalMulai = isset($props['start']) ? $props['start'] : $this->carbon::now();
        $tanggalAkhir = isset($props['end']) ? $props['end'] : $this->carbon::now();

        /* TRANSAKSI PER PERIODE */
        $datas = DB::table('detail_pengeluaran_parkir')
                    ->select(DB::raw("kode_akun AS kode, nama_akun AS keterangan, IFNULL(SUM(jumlah_pengeluaran), 0) AS saldo"))
                    ->where('detail_pengeluaran_parkir.tanggal', '>=', $tanggalMulai)
                    ->where('detail_pengeluaran_parkir.tanggal', '<=', $tanggalAkhir)
                    ->groupBy('kode_akun', 'nama_akun')
                    ->get();

        /* SALDO AWAL */
        $saldoAwal = DB::table('detail_pengeluaran_parkir')
                    ->select(DB::raw("kode_akun AS kode, nama_akun AS keterangan, IFNULL(SUM(jumlah_pengeluaran), 0) AS saldo"))
                    ->where('detail_pengeluaran_parkir.tanggal', '<', $tanggalMulai)
                    ->groupBy('kode_akun', 'nama_akun')
                    ->get();

        $arusKasPengeluaran = [
            [
                'id'        => 'pengeluaran',
                'nama'      => 'Pengeluaran dari Aktivitas Operasional',
                'transaksi' => $datas,
                'saldo_awal'=> $saldoAwal
            ]
        ];
        return $arusKasPengeluaran;
    }
}
