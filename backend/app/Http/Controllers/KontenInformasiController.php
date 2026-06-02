<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KontenInformasiController extends Controller
{
    // 1. TAMPILAN WEB ADMIN: List Konten
    public function index()
    {
        $konten = DB::table('konten_informasi')->latest()->get();
        return view('kelola-konten', compact('konten'));
    }

    // 2. TAMPILAN WEB ADMIN: Simpan Konten Baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi file gambar
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan gambar ke dalam folder storage/app/public/konten
            $imagePath = $request->file('image')->store('konten', 'public');
        }

        DB::table('konten_informasi')->insert([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath, // SIMPAN PATH GAMBAR
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Konten informasi baru berhasil ditambahkan!');
    }

    // 3. TAMPILAN WEB ADMIN: Update Konten
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $currentData = DB::table('konten_informasi')->where('id', $id)->first();
        $imagePath = $currentData->image;

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari server jika sebelumnya ada gambar
            if ($currentData->image) {
                Storage::disk('public')->delete($currentData->image);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('konten', 'public');
        }

        DB::table('konten_informasi')->where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath, // UPDATE PATH GAMBAR
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Konten informasi berhasil diperbarui!');
    }

    // 4. TAMPILAN WEB ADMIN: Hapus Konten
    public function destroy($id)
    {
        $currentData = DB::table('konten_informasi')->where('id', $id)->first();
        
        // Hapus file gambar dari server sebelum datanya dihapus dari tabel
        if ($currentData && $currentData->image) {
            Storage::disk('public')->delete($currentData->image);
        }

        DB::table('konten_informasi')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Konten informasi berhasil dihapus!');
    }

    // ==========================================
    // 5. API ENDPOINT UNTUK REACT NATIVE MOBILE
    // ==========================================
    public function getApiInformasi()
    {
        $data = DB::table('konten_informasi')
            ->select('id', 'title', 'description', 'image') // Menghapus select field icon
            ->get();

        return response()->json($data, 200);
    }
}