<?php
namespace App\Http\Controllers\Pendapatan;

use Exception;
use Illuminate\Http\Request;
use App\Http\Services\Pendapatan\PendapatanService;
use App\Http\Controllers\BaseController;

class PendapatanController extends BaseController
{
    private $pendapatanServices;
    private $moduleName;

    public function __construct(PendapatanService $pendapatanServices)
    {
        $this->pendapatanServices = $pendapatanServices;
        $this->moduleName = 'Pendapatan Parkir';
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $pendapatan = $this->pendapatanServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar pendapatan parkir', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'tanggal'   => 'required|date',
                'gambar'    => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $pendapatan = $this->pendapatanServices->createPendapatan($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'Pendapatan parkir berhasil dibuat', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $pendapatan = $this->pendapatanServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail pendapatan parkir', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'tanggal'   => 'required|date',
                'gambar'    => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $pendapatan = $this->pendapatanServices->updatePendapatan($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Pendapatan parkir berhasil diperbaharui', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $pendapatan = $this->pendapatanServices->destroyPendapatan($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Pendapatan parkir berhasil dihapus!', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $pendapatan = $this->pendapatanServices->destroyMultiplePendapatan($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Pendapatan parkir berhasil dihapus!', $pendapatan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
