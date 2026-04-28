<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Tes - STIFIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('pendaftaran-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>
            <a href="{{ route('jadwal-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>
            <a href="{{ route('hasil-tes') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                <i class="fas fa-file-medical mr-3"></i> Hasil Tes
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-chart-bar mr-3"></i> Laporan
            </a>
            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-800 mt-4 pt-4">
                @csrf
                <button type="submit" class="w-full text-left py-3 px-6 text-red-400 hover:bg-red-900/20 transition">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="flex-1">
        <header class="bg-white shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Hasil Tes</h2>
        </header>

        <main class="p-8">
            <div class="flex space-x-4 mb-8 border-b">
                <a href="?tab=kelola" class="py-2 px-6 font-semibold {{ $tab == 'kelola' ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-400' }}">
                    <i class="fas fa-tasks mr-2"></i> Kelola Hasil Tes
                </a>
                <a href="?tab=riwayat" class="py-2 px-6 font-semibold {{ $tab == 'riwayat' ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-400' }}">
                    <i class="fas fa-history mr-2"></i> Riwayat Tes
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nama Klien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tipe Tes</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Hasil</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-bold text-gray-900">{{ $item->nama }}</div>
                                <div class="text-gray-500">{{ $item->no_hp }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->tipe_tes ?? 'Tes STIFIn' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($item->status_tes == 'Selesai')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-xs">{{ $item->hasil }}</span>
                                @else
                                    <span class="text-gray-400 italic">Belum diinput</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($tab == 'kelola')
                                    <button onclick="openModal('{{ $item->id_tes }}', '{{ $item->nama }}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-blue-700 transition">
                                        <i class="fas fa-check-circle mr-1"></i> Input Hasil
                                    </button>
                                @else
                                    <div class="flex justify-center space-x-2 text-sm">
                                        <a href="{{ asset('uploads/hasil/' . $item->file_hasil) }}" download class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button onclick="previewFile('{{ asset('uploads/hasil/' . $item->file_hasil) }}')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Data tidak ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modalHasil" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-40">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
            <h3 class="text-lg font-bold mb-4">Input Hasil Tes: <span id="modalNama" class="text-blue-600"></span></h3>
            <form id="formHasil" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-4 text-sm font-medium">
                    <label class="block mb-1">Pilih Hasil Mesin Kecerdasan</label>
                    <select name="hasil" class="w-full border rounded-lg p-2 bg-gray-50 focus:ring-2 focus:ring-blue-500" required>
                        <option value="Sensing">Sensing</option>
                        <option value="Thinking">Thinking</option>
                        <option value="Intuiting">Intuiting</option>
                        <option value="Feeling">Feeling</option>
                        <option value="Instinct">Instinct</option>
                    </select>
                </div>
                <div class="mb-6 text-sm font-medium text-gray-700">
                    <label class="block mb-1">Unggah Sertifikat (PDF/Gambar)</label>
                    <input type="file" name="file_hasil" class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                <div class="flex justify-end space-x-3 text-sm font-semibold text-gray-500">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 hover:text-gray-800 transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md">Simpan & Selesaikan</button>
                </div>
            </form>
        </div>
    </div>

   <div id="modalPreview" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-6 z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[95vh] flex flex-col overflow-hidden border border-gray-300">
        
        <div class="p-4 border-b flex justify-between items-center bg-white">
            <div class="flex items-center space-x-2">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
                <h3 class="font-bold text-gray-800">Preview Sertifikat Hasil Tes</h3>
            </div>
            <button onclick="closePreview()" class="text-gray-400 hover:text-red-500 transition-colors text-3xl px-2">&times;</button>
        </div>

        <div class="flex-1 bg-slate-100 relative overflow-hidden">
            <iframe id="previewFrame" src="" class="w-full h-full border-none shadow-inner" style="min-height: 60vh;"></iframe>
        </div>

        <div class="p-4 border-t bg-gray-50 flex justify-end items-center gap-3">
            <span class="text-xs text-gray-500 mr-auto italic">*Gunakan tombol unduh di tabel jika ingin menyimpan file.</span>
            
            <button onclick="closePreview()" 
                class="px-8 py-2.5 bg-slate-800 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95">
                Tutup
            </button>
        </div>
    </div>
</div>

    <script>
        // Logic Modal Input
        function openModal(id, nama) {
            document.getElementById('modalHasil').classList.remove('hidden');
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('formHasil').action = '/hasil-tes/' + id;
        }
        function closeModal() {
            document.getElementById('modalHasil').classList.add('hidden');
        }

        // Logic Modal Preview
        function previewFile(url) {
            const modal = document.getElementById('modalPreview');
            const frame = document.getElementById('previewFrame');
    
            frame.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'animate-in', 'fade-in', 'duration-300'); // Tambahkan animasi tailwind
            document.body.style.overflow = 'hidden';
        }
        function closePreview() {
            const modal = document.getElementById('modalPreview');
            const frame = document.getElementById('previewFrame');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            frame.src = "";
            document.body.style.overflow = 'auto'; // Aktifkan scroll kembali
        }
    </script>
</body>
</html>