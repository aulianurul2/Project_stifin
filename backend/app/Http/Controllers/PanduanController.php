<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanduanController extends Controller
{
    public function index()
    {
        $panduan = Panduan::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar panduan berhasil diambil',
            'data' => $panduan
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'icon' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $panduan = Panduan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Panduan berhasil ditambahkan',
            'data' => $panduan
        ], 210);
    }

    public function show($id)
    {
        $panduan = Panduan::find($id);

        if (!$panduan) {
            return response()->json([
                'success' => false,
                'message' => 'Panduan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail panduan berhasil diambil',
            'data' => $panduan
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $panduan = Panduan::find($id);

        if (!$panduan) {
            return response()->json([
                'success' => false,
                'message' => 'Panduan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'icon' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $panduan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Panduan berhasil diperbarui',
            'data' => $panduan
        ], 200);
    }

    public function destroy($id)
    {
        $panduan = Panduan::find($id);

        if (!$panduan) {
            return response()->json([
                'success' => false,
                'message' => 'Panduan tidak ditemukan'
            ], 404);
        }

        $panduan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Panduan berhasil dihapus'
        ], 200);
    }
}