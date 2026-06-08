<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        // Menampilkan semua slot jadwal yang dibuat oleh admin
        $jadwal = DB::table('jadwal')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        return view('jadwal-tes', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required',
            'kuota'   => 'required|integer',
            'lokasi'  => 'required',
        ]);

        DB::table('jadwal')->insert([
            'tanggal'    => $request->tanggal,
            'waktu'      => $request->waktu,
            'kuota'      => $request->kuota,
            'lokasi'     => $request->lokasi,
            'status'     => 'Tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Slot jadwal berhasil diterbitkan!');
    }

    public function destroy($id)
    {
        DB::table('jadwal')->where('id_jadwal', $id)->delete();
        // Ikut menghapus data tes berkaitan jika slot dihapus paksa oleh admin
        DB::table('hasiltes')->where('id_jadwal', $id)->delete();

        return redirect()->back()->with('success', 'Slot jadwal berhasil dihapus');
    }

    /**
     * API Internal untuk mengambil daftar pendaftar berdasarkan ID Jadwal (Untuk Popup Modal)
     */
    public function getKlienByJadwal($id)
    {
        $jadwal = DB::table('jadwal')
            ->where('id_jadwal', $id)
            ->select(
                'id_jadwal',
                'nama_klien',
                'no_hp',
                'status as status_jadwal',
                'komentar'
            )
            ->get();

        return response()->json($jadwal);
    }

    /**
     * API React Native — jadwal tersedia mulai hari ini ke depan
     */
public function getJadwalApi()
{
    $sekarang = now(); // pakai timezone dari config/app.php

    $data = DB::table('jadwal')
        ->where('status', 'Tersedia')
        ->whereDate('tanggal', '>=', $sekarang->toDateString())
        ->orderBy('tanggal', 'asc')
        ->orderBy('waktu', 'asc')
        ->get()
        ->filter(function ($item) use ($sekarang) {
            $jadwalDateTime = \Carbon\Carbon::parse($item->tanggal . ' ' . $item->waktu);
            return $jadwalDateTime->isFuture(); // saring yang jamnya sudah lewat
        })
        ->map(function ($item) {
            $item->waktu = \Carbon\Carbon::parse($item->waktu)->format('H:i');
            return $item;
        })
        ->values(); // reset index supaya JSON tidak jadi object

    return response()->json($data);
}

  public function updateStatus(Request $request, $id)
{
    try {
        if ($request->status == 'Ditolak') {
            DB::table('jadwal')->where('id_jadwal', $id)->update([
                'status'     => 'Ditolak',
                'updated_at' => now(),
            ]);

        } elseif ($request->status == 'Tersedia') {

            // Ambil kolom yang benar-benar ada di tabel jadwal
            $columns = DB::getSchemaBuilder()->getColumnListing('jadwal');

            $resetData = [
                'status'         => 'Tersedia',
                'nama_klien'     => null,
                'no_hp'          => null,
                'email'          => null,
                'alamat'         => null,
                'komentar'       => null,
                'id_klien'       => null,
                'tanggal_lahir'  => null,
                'jenis_kelamin'  => null,
                'golongan_darah' => null,
                'domisili'       => null,
                'institusi'      => null,
                'sosmed'         => null,
                'bukti_transfer' => null,
                'is_luar_subang' => 0,   // ← pakai 0 bukan false
                'nama_kota'      => null,
                'biaya'          => 0,
                'kuota'          => 1,
                'updated_at'     => now(),
            ];

            // Filter: hanya update kolom yang ada di tabel
            $filteredData = array_filter(
                $resetData,
                fn($key) => in_array($key, $columns),
                ARRAY_FILTER_USE_KEY
            );

            DB::table('jadwal')->where('id_jadwal', $id)->update($filteredData);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui!']);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui status: ' . $e->getMessage()
        ], 500);
    }
}
}