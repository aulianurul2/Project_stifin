<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    // =========================================================
    // WEB ADMIN
    // =========================================================

    /**
     * Tampilan daftar pendaftaran untuk Admin di Web.
     */
    public function index()
    {
        $pendaftaran = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->orderBy('id_jadwal', 'desc')
            ->get();

        return view('pendaftaran-tes', compact('pendaftaran'));
    }

    // =========================================================
    // API — REACT NATIVE
    // =========================================================

    /**
     * Submit pendaftaran dari React Native.
     * Menerima multipart/form-data karena ada upload file_bukti.
     */
    public function storeAPI(Request $request)
    {
        $request->validate([
            'id_jadwal'      => 'required|integer',
            'is_luar_subang' => 'required|boolean',
            'file_bukti'     => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nama_klien'     => 'required|string',
            'no_hp'          => 'required|string',
            'email'          => 'required|email',
            'tanggal_lahir'  => 'nullable|date',
            'jenis_kelamin'  => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'domisili'       => 'nullable|string',
            'institusi'      => 'nullable|string',
            'sosmed'         => 'nullable|string',
            'alamat'         => 'required|string',
        ]);

        // Pastikan direktori tujuan ada
        if (!file_exists(public_path('uploads/bukti'))) {
            mkdir(public_path('uploads/bukti'), 0775, true);
        }

        // Simpan file bukti transfer
        $file     = $request->file('file_bukti');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/bukti'), $namaFile);

        // Ambil data klien berdasarkan user yang login
        $user  = $request->user();
        $klien = DB::table('klien')->where('id_user', $user->id_user)->first();

        if (!$klien) {
            return response()->json(['message' => 'Data klien tidak ditemukan.'], 404);
        }

        // Cek kuota jadwal
        $jadwal = DB::table('jadwal')->where('id_jadwal', $request->id_jadwal)->first();

        if (!$jadwal || $jadwal->kuota <= 0) {
            return response()->json(['message' => 'Slot kuota untuk jadwal ini sudah penuh.'], 400);
        }

        // Update jadwal dengan data pendaftaran
        $updated = DB::table('jadwal')
            ->where('id_jadwal', $request->id_jadwal)
            ->update([
                'is_luar_subang' => (bool) $request->is_luar_subang,
                'nama_kota'      => $request->nama_kota ?? null,
                'biaya'          => (int) $request->biaya,
                'bukti_transfer' => $namaFile,
                'id_klien'       => $klien->id_klien,
                'nama_klien'     => $request->nama_klien,
                'no_hp'          => $request->no_hp,
                'email'          => $request->email,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'golongan_darah' => $request->golongan_darah,
                'domisili'       => $request->domisili,
                'institusi'      => $request->institusi,
                'sosmed'         => $request->sosmed,
                'alamat'         => $request->alamat,
                'status'         => 'Menunggu',
                'kuota'          => $jadwal->kuota - 1,
                'updated_at'     => now(),
            ]);

        if ($updated) {
            return response()->json(['message' => 'Pendaftaran berhasil.'], 200);
        }

        return response()->json(['message' => 'Gagal menyimpan pendaftaran.'], 500);
    }

    /**
     * Riwayat pendaftaran milik klien yang login.
     */
    public function getRiwayat(Request $request)
    {
        $user  = $request->user();
        $klien = DB::table('klien')->where('id_user', $user->id_user)->first();

        if (!$klien) {
            return response()->json([]);
        }

        $riwayat = DB::table('jadwal')
            ->leftJoin('hasiltes', 'jadwal.id_jadwal', '=', 'hasiltes.id_jadwal')
            ->select(
                'jadwal.*',
                'hasiltes.status_tes',
                'hasiltes.file_hasil',
                'hasiltes.file_detail'
            )
            ->where('jadwal.id_klien', $klien->id_klien)
            ->orderBy('jadwal.updated_at', 'desc')
            ->get();

        return response()->json($riwayat);
    }

    /**
     * Hasil tes milik klien yang login.
     */
    public function hasilTesSaya(Request $request)
    {
        $user  = $request->user();
        $klien = DB::table('klien')->where('id_user', $user->id_user)->first();

        if (!$klien) {
            return response()->json([]);
        }

        $data = DB::table('hasiltes')
            ->join('jadwal', 'hasiltes.id_jadwal', '=', 'jadwal.id_jadwal')
            ->where('hasiltes.id_klien', $klien->id_klien)
            ->select(
                'hasiltes.*',
                'jadwal.id_jadwal',
                'jadwal.tanggal',
                'jadwal.waktu',
                'jadwal.status',
                'jadwal.komentar'
            )
            ->orderBy('hasiltes.id_tes', 'desc')
            ->get();

        return response()->json($data);
    }

    // =========================================================
    // WEB ADMIN — Update Status Pendaftaran
    // =========================================================

    /**
     * Update status pendaftaran oleh admin dari dashboard web.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'   => 'required|string',
            'komentar' => 'nullable|string',
        ]);

        $statusInput = strtolower(trim($request->status));

        $jadwalLama = DB::table('jadwal')->where('id_jadwal', $id)->first();

        if (!$jadwalLama) {
            return redirect()->back()->with('error', 'Data jadwal tidak ditemukan.');
        }

        // 1. DITOLAK — kembalikan kuota, hapus hasiltes
        if ($statusInput == 'ditolak') {
            $kuotaBaru = (strtolower($jadwalLama->status) == 'ditolak')
                ? $jadwalLama->kuota
                : $jadwalLama->kuota + 1;

            DB::table('jadwal')->where('id_jadwal', $id)->update([
                'status'     => 'Ditolak',
                'komentar'   => $request->komentar,
                'kuota'      => $kuotaBaru,
                'updated_at' => now(),
            ]);

            DB::table('hasiltes')->where('id_jadwal', $id)->delete();

        // 2. BUKA KEMBALI — kosongkan data klien, kembalikan kuota
        } elseif ($statusInput == 'tersedia') {
            DB::table('jadwal')->where('id_jadwal', $id)->update([
                'id_klien'       => null,
                'nama_klien'     => null,
                'no_hp'          => null,
                'email'          => null,
                'alamat'         => null,
                'tanggal_lahir'  => null,
                'jenis_kelamin'  => null,
                'golongan_darah' => null,
                'domisili'       => null,
                'institusi'      => null,
                'sosmed'         => null,
                'bukti_transfer' => null,
                'is_luar_subang' => false,
                'nama_kota'      => null,
                'biaya'          => null,
                'status'         => 'Tersedia',
                'komentar'       => null,
                'kuota'          => 1,
                'updated_at'     => now(),
            ]);

            DB::table('hasiltes')->where('id_jadwal', $id)->delete();

        // 3. STATUS LAINNYA (Menunggu, dll.)
        } else {
            DB::table('jadwal')->where('id_jadwal', $id)->update([
                'status'     => $request->status,
                'komentar'   => $request->komentar,
                'updated_at' => now(),
            ]);
        }

        // 4. DITERIMA — buat/update record hasiltes
        if ($request->status == 'Diterima') {
            $klien = DB::table('klien')->where('id_klien', $jadwalLama->id_klien)->first();

            if (!$klien) {
                $id_klien = DB::table('klien')->insertGetId([
                    'nama'       => $jadwalLama->nama_klien,
                    'no_hp'      => $jadwalLama->no_hp,
                    'email'      => $jadwalLama->email,
                    'alamat'     => $jadwalLama->alamat,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $id_klien = $klien->id_klien;
            }

            $exists = DB::table('hasiltes')->where('id_jadwal', $id)->exists();

            if (!$exists) {
                DB::table('hasiltes')->insert([
                    'id_jadwal'  => $id,
                    'id_klien'   => $id_klien,
                    'status_tes' => 'Proses',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('hasiltes')->where('id_jadwal', $id)->update([
                    'status_tes' => 'Proses',
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    // =========================================================
    // API — Pembatalan dari Klien
    // =========================================================

    /**
     * Batalkan pendaftaran oleh klien — kosongkan slot dan kembalikan kuota.
     */
    public function batalkanPendaftaranApi(Request $request, $id)
    {
        $jadwal = DB::table('jadwal')->where('id_jadwal', $id)->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Data pendaftaran tidak ditemukan.'], 404);
        }

        $updated = DB::table('jadwal')->where('id_jadwal', $id)->update([
            'id_klien'       => null,
            'nama_klien'     => null,
            'no_hp'          => null,
            'email'          => null,
            'tanggal_lahir'  => null,
            'jenis_kelamin'  => null,
            'golongan_darah' => null,
            'domisili'       => null,
            'institusi'      => null,
            'sosmed'         => null,
            'alamat'         => null,
            'bukti_transfer' => null,
            'is_luar_subang' => false,
            'nama_kota'      => null,
            'biaya'          => null,
            'status'         => 'Tersedia',
            'komentar'       => null,
            'kuota'          => $jadwal->kuota + 1,
            'updated_at'     => now(),
        ]);

        if ($updated) {
            DB::table('hasiltes')->where('id_jadwal', $id)->delete();
            DB::table('pemasukan')->where('id_jadwal', $id)->delete();
            return response()->json(['message' => 'Pendaftaran berhasil dibatalkan, slot kembali tersedia.'], 200);
        }

        return response()->json(['message' => 'Gagal membatalkan pendaftaran.'], 500);
    }

    // =========================================================
    // API — Reschedule dari Klien
    // =========================================================

    /** Batas maksimal hari setelah tanggal jadwal untuk mengajukan reschedule */
    private const BATAS_RESCHEDULE_HARI = 14;

    /**
     * Ajukan reschedule ke slot jadwal lain.
     * Validasi: maksimal 14 hari setelah tanggal jadwal lama.
     */
    public function reschedulePendaftaranApi(Request $request, $id)
    {
        $request->validate([
            'id_jadwal_baru' => 'required|integer',
            'is_luar_subang' => 'nullable',
            'nama_kota'      => 'nullable|string',
            'biaya'          => 'nullable|integer|min:0',
        ]);

        $id_jadwal_lama = (int) $id;
        $id_jadwal_baru = (int) $request->id_jadwal_baru;

        $pendaftaranLama = DB::table('jadwal')->where('id_jadwal', $id_jadwal_lama)->first();
        $jadwalBaru      = DB::table('jadwal')->where('id_jadwal', $id_jadwal_baru)->first();

        if (!$pendaftaranLama) {
            return response()->json(['message' => 'Data pendaftaran asal tidak ditemukan.'], 404);
        }

        if (is_null($pendaftaranLama->id_klien)) {
            return response()->json(['message' => 'Pendaftaran ini sudah tidak aktif atau sudah pernah di-reschedule.'], 400);
        }

        // ── Validasi batas 14 hari ────────────────────────────────────────────
        if (!empty($pendaftaranLama->tanggal)) {
            try {
                // Support format "DD/MM/YYYY" maupun format ISO/DB lainnya
                $tanggalStr = trim($pendaftaranLama->tanggal);
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalStr)) {
                    $tanggalJadwal = Carbon::createFromFormat('d/m/Y', $tanggalStr)->startOfDay();
                } else {
                    $tanggalJadwal = Carbon::parse($tanggalStr)->startOfDay();
                }

                $hariSelisih = Carbon::today()->diffInDays($tanggalJadwal, false) * -1;
                // diffInDays(false) → negatif jika jadwal sudah lewat
                // Kita balik tanda: positif = sudah lewat N hari

                if ($hariSelisih > self::BATAS_RESCHEDULE_HARI) {
                    return response()->json([
                        'message' => 'Batas waktu reschedule telah terlewat. Reschedule hanya dapat diajukan maksimal '
                                   . self::BATAS_RESCHEDULE_HARI . ' hari setelah tanggal jadwal. '
                                   . 'Jadwal Anda telah lewat ' . $hariSelisih . ' hari.',
                    ], 422);
                }
            } catch (\Exception $e) {
                // Jika format tanggal tidak bisa di-parse, biarkan lanjut
                // (tidak blokir karena data lama mungkin format berbeda)
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        if (!$jadwalBaru || $jadwalBaru->kuota <= 0 || !is_null($jadwalBaru->id_klien)) {
            return response()->json(['message' => 'Jadwal baru yang dipilih sudah penuh atau tidak tersedia.'], 400);
        }

        if ($id_jadwal_lama === $id_jadwal_baru) {
            return response()->json(['message' => 'Jadwal baru tidak boleh sama dengan jadwal saat ini.'], 400);
        }

        $isLuarSubang = false;
        if ($request->has('is_luar_subang')) {
            $val = $request->input('is_luar_subang');
            $isLuarSubang = ($val === true || $val === 'true' || $val == 1 || $val === '1');
        } else {
            $isLuarSubang = (bool) $pendaftaranLama->is_luar_subang;
        }

        try {
            DB::transaction(function () use (
                $id_jadwal_lama, $id_jadwal_baru,
                $pendaftaranLama, $jadwalBaru,
                $request, $isLuarSubang
            ) {
                // Hapus manual dulu — CASCADE tidak jalan saat UPDATE
                DB::table('hasiltes')->where('id_jadwal', $id_jadwal_lama)->delete();
                DB::table('pemasukan')->where('id_jadwal', $id_jadwal_lama)->delete();

                // Step 1: Kosongkan jadwal LAMA
                DB::table('jadwal')->where('id_jadwal', $id_jadwal_lama)->update([
                    'id_klien'       => null,
                    'nama_klien'     => null,
                    'no_hp'          => null,
                    'email'          => null,
                    'alamat'         => null,
                    'tanggal_lahir'  => null,
                    'jenis_kelamin'  => null,
                    'golongan_darah' => null,
                    'domisili'       => null,
                    'institusi'      => null,
                    'sosmed'         => null,
                    'bukti_transfer' => null,
                    'is_luar_subang' => 0,
                    'nama_kota'      => null,
                    'biaya'          => 550000,
                    'status'         => 'Tersedia',
                    'komentar'       => null,
                    'kuota'          => $pendaftaranLama->kuota + 1,
                    'updated_at'     => now(),
                ]);

                // Step 2: Isi jadwal BARU
                DB::table('jadwal')->where('id_jadwal', $id_jadwal_baru)->update([
                    'id_klien'       => $pendaftaranLama->id_klien,
                    'nama_klien'     => $pendaftaranLama->nama_klien,
                    'no_hp'          => $pendaftaranLama->no_hp,
                    'email'          => $pendaftaranLama->email,
                    'tanggal_lahir'  => $pendaftaranLama->tanggal_lahir,
                    'jenis_kelamin'  => $pendaftaranLama->jenis_kelamin,
                    'golongan_darah' => $pendaftaranLama->golongan_darah,
                    'domisili'       => $pendaftaranLama->domisili,
                    'institusi'      => $pendaftaranLama->institusi,
                    'sosmed'         => $pendaftaranLama->sosmed,
                    'alamat'         => $pendaftaranLama->alamat,
                    'bukti_transfer' => $pendaftaranLama->bukti_transfer,
                    'is_luar_subang' => $isLuarSubang ? 1 : 0,
                    'nama_kota'      => $request->has('nama_kota')
                                            ? $request->nama_kota
                                            : $pendaftaranLama->nama_kota,
                    'biaya'          => $request->has('biaya')
                                            ? (int) $request->biaya
                                            : (int) $pendaftaranLama->biaya,
                    'status'         => 'Menunggu',
                    'kuota'          => $jadwalBaru->kuota - 1,
                    'komentar'       => 'Reschedule dari jadwal tanggal: ' . $pendaftaranLama->tanggal,
                    'updated_at'     => now(),
                ]);
            });

            return response()->json(['message' => 'Reschedule berhasil diajukan.'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses reschedule.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}