<?php
namespace App\Http\Controllers\Pengeluaran;

use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Pengeluaran\DetailPengeluaranService;
use App\Http\Controllers\BaseController;

class DetailPengeluaranController extends BaseController
{
    private $detailServices;
    private $moduleName;

    public function __construct(DetailPengeluaranService $detailServices)
    {
        $this->detailServices = $detailServices;
        $this->moduleName = 'Detail Pengeluaran Parkir';
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $props += [
                'id'  => $request['id'] ?? null
            ];
            $detail = $this->detailServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar detail pengeluaran parkir', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'id'                    => 'required',
                'tanggal'               => 'required|date',
                'kode_akun'             => 'required',
                'nama_akun'             => 'required',
                'jumlah_pengeluaran'    => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $detail = $this->detailServices->createDetail($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'Detail pengeluaran parkir berhasil dibuat', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $detail = $this->detailServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail pengeluaran parkir', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'id'                    => 'required',
                'tanggal'               => 'required|date',
                'kode_akun'             => 'required',
                'kode_akun'             => 'required',
                'nama_akun'             => 'required',
                'jumlah_pengeluaran'    => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $detail = $this->detailServices->updateDetail($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail pengeluaran parkir berhasil diperbaharui', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $detail = $this->detailServices->destroyDetail($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail pengeluaran parkir berhasil dihapus!', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $detail = $this->detailServices->destroyMultipleDetail($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Detail pengeluaran parkir berhasil dihapus!', $detail);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
