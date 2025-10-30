<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('service.index', [
            'title' => 'Service',
            'service' => Service::where('void', 0)->get(),
        ]);
    }

    public function addService(Request $request)
    {
        Service::create([
            'nm_service' => $request->nm_service,
            'harga' => $request->harga,
            'pembagian' => $request->pembagian,
            'void' => 0
        ]);

        return redirect()->back()->with('success', 'Data service berhasil dibuat');
    }

    public function editService(Request $request)
    {
        Service::where('id', $request->id)->update([
            'nm_service' => $request->nm_service,
            'harga' => $request->harga,
            'pembagian' => $request->pembagian,
        ]);

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
