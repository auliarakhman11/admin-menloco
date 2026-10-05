<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Cabang;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('service.index', [
            'title' => 'Service',
            'service' => Service::with('cabang')->where('void', 0)->get(),
            'cabang' => Cabang::all(),
        ]);
    }

    public function addService(Request $request)
    {
        $service = Service::create([
            'nm_service' => $request->nm_service,
            'jenis' => $request->jenis,
            'harga' => $request->harga,
            'pembagian' => $request->pembagian,
            'void' => 0
        ]);

        if ($request->cabang_id) {
            $service->cabang()->sync($request->cabang_id);
        }

        return redirect()->back()->with('success', 'Data service berhasil dibuat');
    }

    public function editService(Request $request)
    {
        $service = Service::find($request->id);
        if ($service) {
            $service->update([
                'nm_service' => $request->nm_service,
                'jenis' => $request->jenis,
                'harga' => $request->harga,
                'pembagian' => $request->pembagian,
            ]);

            $service->cabang()->sync($request->cabang_id ?? []);
        }

        return redirect()->back()->with('success', 'Data service berhasil diubah');
    }

    public function deleteService($id)
    {
        Service::where('id', $id)->update([
            'void' => 1
        ]);

        return redirect()->back()->with('success', 'Data service berhasil dihapus');
    }
}
