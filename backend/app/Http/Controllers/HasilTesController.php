<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilTesController extends Controller
{
    /**
     * Menampilkan halaman hasil tes (tab Kelola & Riwayat).
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'kelola');

        $query = DB::table('hasiltes')
            ->leftJoin('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('hasiltes.*', 'klien.nama', 'klien.no_hp')
            ->orderBy('hasiltes.id_tes', 'desc');

        if ($tab == 'kelola') {
            $data = $query->where('hasiltes.status_tes', 'Proses')->get();
        } else {
            $data = $query->where('hasiltes.status_tes', 'Selesai')->paginate(10)->withQueryString();
        }

        return view('hasil-tes', compact('data', 'tab'));
    }

    /**
     * Upload file hasil tes oleh admin dan catat ke tabel pemasukan.
     */
public function update(Request $request, $id)
{
    $request->validate([
        'file_hasil'  => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        'file_detail' => 'required|mimes:pdf,doc,docx|max:5120',
        'id_jadwal'   => 'required|integer',
    ]);

    if (!file_exists(public_path('uploads/hasil'))) {
        mkdir(public_path('uploads/hasil'), 0775, true);
    }

    $idJadwal = $request->input('id_jadwal');
    $jadwal   = DB::table('jadwal')->where('id_jadwal', $idJadwal)->first();

    if (!$jadwal) {
        return redirect()->back()->with('error', 'Data jadwal tidak ditemukan.');
    }

    $nominal = $jadwal->is_luar_subang ? 650000 : 550000;

    $data = [
        'status_tes' => 'Selesai',
        'tanggal'    => now(),
        'updated_at' => now(),
        'biaya_tes'  => $nominal, // ← sekarang terisi
    ];

    if ($request->hasFile('file_hasil')) {
        $nama = time() . '_sertifikat_' . $request->file('file_hasil')->getClientOriginalName();
        $request->file('file_hasil')->move(public_path('uploads/hasil'), $nama);
        $data['file_hasil'] = $nama;
    }

    if ($request->hasFile('file_detail')) {
        $nama2 = time() . '_detail_' . $request->file('file_detail')->getClientOriginalName();
        $request->file('file_detail')->move(public_path('uploads/hasil'), $nama2);
        $data['file_detail'] = $nama2;
    }

    DB::table('pemasukan')->insert([
        'id_jadwal'  => $idJadwal,
        'jumlah'     => $nominal,
        'keterangan' => 'Pembayaran Tes ' . ($jadwal->is_luar_subang ? 'Luar Subang' : 'Dalam Subang'),
        'created_at' => now(),
    ]);

    DB::table('hasiltes')->where('id_tes', $id)->update($data);

    return redirect('/hasil-tes?tab=riwayat')->with('success', 'Upload berhasil.');
}
/**
 * Edit/ganti file hasil tes yang sudah diupload.
 */
public function edit(Request $request, $id)
{
    $request->validate([
        'file_hasil'  => 'nullable|mimes:pdf,jpg,png,jpeg|max:2048',
        'file_detail' => 'nullable|mimes:pdf,doc,docx|max:5120',
    ]);

    // Minimal satu file harus dikirim
    if (!$request->hasFile('file_hasil') && !$request->hasFile('file_detail')) {
        return redirect()->back()->with('error', 'Pilih minimal satu file yang ingin diganti.');
    }

    if (!file_exists(public_path('uploads/hasil'))) {
        mkdir(public_path('uploads/hasil'), 0775, true);
    }

    // Ambil data lama untuk hapus file lama
    $existing = DB::table('hasiltes')->where('id_tes', $id)->first();

    if (!$existing) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    $data = ['updated_at' => now()];

    if ($request->hasFile('file_hasil')) {
        // Hapus file lama jika ada
        if ($existing->file_hasil && file_exists(public_path('uploads/hasil/' . $existing->file_hasil))) {
            unlink(public_path('uploads/hasil/' . $existing->file_hasil));
        }
        $nama = time() . '_sertifikat_' . $request->file('file_hasil')->getClientOriginalName();
        $request->file('file_hasil')->move(public_path('uploads/hasil'), $nama);
        $data['file_hasil'] = $nama;
    }

    if ($request->hasFile('file_detail')) {
        // Hapus file lama jika ada
        if ($existing->file_detail && file_exists(public_path('uploads/hasil/' . $existing->file_detail))) {
            unlink(public_path('uploads/hasil/' . $existing->file_detail));
        }
        $nama2 = time() . '_detail_' . $request->file('file_detail')->getClientOriginalName();
        $request->file('file_detail')->move(public_path('uploads/hasil'), $nama2);
        $data['file_detail'] = $nama2;
    }

    DB::table('hasiltes')->where('id_tes', $id)->update($data);

    return redirect('/hasil-tes?tab=riwayat')->with('success', 'File berhasil diperbarui.');
}
}