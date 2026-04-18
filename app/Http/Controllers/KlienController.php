<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KlienController extends Controller
{
    public function index()
    {
        $klien = DB::table('klien')
            ->leftJoin('jadwal', 'klien.id_klien', '=', 'jadwal.id_klien')
            ->select('klien.*', 'jadwal.status as status_jadwal')
            ->get();

        return view('kelola-klien', compact('klien'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string'
        ]);

        DB::table('klien')->where('id_klien', $id)->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Data klien berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('klien')->where('id_klien', $id)->delete();
        return redirect()->back()->with('success', 'Klien berhasil dihapus');
    }
}