<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HasilTesController extends Controller
{
    private const BIAYA_TRANSPORT = [
        'Kota Subang' => 25000,
        'Kab. Subang'  => 50000,
    ];
    private const BIAYA_TRANSPORT_LUAR = 75000;
    private const BIAYA_TES = 550000;

    private function hitungNominal(object $jadwal): int
    {
        if (!$jadwal->is_luar_subang) {
            $namaKota  = trim($jadwal->nama_kota ?? '');
            $transport = self::BIAYA_TRANSPORT[$namaKota] ?? 0;
            return self::BIAYA_TES + $transport;
        }
        return self::BIAYA_TES + self::BIAYA_TRANSPORT_LUAR;
    }

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
            $data = $query->where('hasiltes.status_tes', 'Selesai')->get();
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
            'hasil_tes'   => 'required|string|max:50',
        ]);

        if (!file_exists(public_path('uploads/hasil'))) {
            mkdir(public_path('uploads/hasil'), 0775, true);
        }

        $idJadwal = $request->input('id_jadwal');
        $jadwal   = DB::table('jadwal')->where('id_jadwal', $idJadwal)->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Data jadwal tidak ditemukan.');
        }

        $nominal  = $this->hitungNominal($jadwal);
        $namaKota = trim($jadwal->nama_kota ?? '');

        if (!$jadwal->is_luar_subang) {
            $namaKota   = trim($jadwal->nama_kota ?? '');
            $transport  = self::BIAYA_TRANSPORT[$namaKota] ?? 0;
            $keterangan = 'Pembayaran Tes' . ($namaKota !== '' ? ' – ' . $namaKota : ' Dalam Subang');
        } else {
            $namaKota   = '';
            $keterangan = 'Pembayaran Tes Luar Subang (Home Visit)';
        }

        $data = [
            'status_tes' => 'Selesai',
            'tanggal'    => now(),
            'updated_at' => now(),
            'biaya_tes'  => $nominal,
            'nama_kota'  => $namaKota ?: null,
            'hasil_tes'  => $request->input('hasil_tes'),
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
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);

        DB::table('hasiltes')->where('id_tes', $id)->update($data);

        return redirect('/hasil-tes?tab=riwayat')->with('success', 'Upload berhasil.');
    }

    /**
     * Edit/ganti file hasil tes dan/atau hasil STIFIn.
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'file_hasil'  => 'nullable|mimes:pdf,jpg,png,jpeg|max:2048',
            'file_detail' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'hasil_tes'   => 'nullable|string|max:50',
        ]);

        // Minimal satu field harus diisi
        if (!$request->hasFile('file_hasil') && !$request->hasFile('file_detail') && !$request->filled('hasil_tes')) {
            return redirect()->back()->with('error', 'Pilih minimal satu perubahan yang ingin disimpan.');
        }

        if (!file_exists(public_path('uploads/hasil'))) {
            mkdir(public_path('uploads/hasil'), 0775, true);
        }

        $existing = DB::table('hasiltes')->where('id_tes', $id)->first();

        if (!$existing) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $data = ['updated_at' => now()];

        // Simpan hasil_tes jika diisi
        if ($request->filled('hasil_tes')) {
            $data['hasil_tes'] = $request->input('hasil_tes');
        }

        if ($request->hasFile('file_hasil')) {
            if ($existing->file_hasil && file_exists(public_path('uploads/hasil/' . $existing->file_hasil))) {
                unlink(public_path('uploads/hasil/' . $existing->file_hasil));
            }
            $nama = time() . '_sertifikat_' . $request->file('file_hasil')->getClientOriginalName();
            $request->file('file_hasil')->move(public_path('uploads/hasil'), $nama);
            $data['file_hasil'] = $nama;
        }

        if ($request->hasFile('file_detail')) {
            if ($existing->file_detail && file_exists(public_path('uploads/hasil/' . $existing->file_detail))) {
                unlink(public_path('uploads/hasil/' . $existing->file_detail));
            }
            $nama2 = time() . '_detail_' . $request->file('file_detail')->getClientOriginalName();
            $request->file('file_detail')->move(public_path('uploads/hasil'), $nama2);
            $data['file_detail'] = $nama2;
        }

        DB::table('hasiltes')->where('id_tes', $id)->update($data);

        return redirect('/hasil-tes?tab=riwayat')->with('success', 'Data berhasil diperbarui.');
    }
}