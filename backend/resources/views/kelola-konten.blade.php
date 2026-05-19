<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konten Informasi STIFIn - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex" x-data="{ openAddModal: false, openEditModal: false, currentInfo: {} }">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('pendaftaran-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>
            <a href="{{ route('jadwal-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>

            <a href="{{ route('kelola-konten.index') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                <i class="fas fa-file-medical mr-3"></i> Kelola Konten
            </a>

            <a href="{{ route('hasil-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
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

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8">
            <h2 class="font-semibold text-gray-800 text-xl">Kelola Konten Informasi</h2>
            <div class="text-sm text-gray-500">Admin: <span class="font-bold">{{ Auth::user()->nama }}</span></div>
        </header>

        <main class="p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">Kartu Informasi Utama</h1>
                    <p class="text-sm text-gray-500 mt-1">Atur kartu slider informasi yang muncul di halaman utama aplikasi mobile user.</p>
                </div>
                <button @click="openAddModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-lg hover:bg-blue-700 transition shadow-sm cursor-pointer">
                    <i class="fas fa-plus"></i> Tambah Kartu Baru
                </button>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-800 text-sm font-medium">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($konten as $item)
                    <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between min-h-[220px] shadow-sm relative transition hover:shadow-md" style="background-color: {{ $item->color }}">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-mono bg-white/60 px-2 py-1 rounded border border-slate-200/40 font-bold" style="color: {{ $item->text_color }}">
                                    <i class="fas fa-cube mr-1"></i> {{ $item->icon }}
                                </span>
                                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-white/70" style="color: {{ $item->text_color }}">Penting</span>
                            </div>

                            @if($item->image)
                                <div class="mb-3 w-full h-32 rounded-lg overflow-hidden bg-white/50 border border-black/5 shadow-xs">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover" alt="Preview Image">
                                </div>
                            @endif

                            <h3 class="text-base font-bold mb-2" style="color: {{ $item->text_color }}">{{ $item->title }}</h3>
                            <p class="text-xs leading-relaxed text-slate-700 line-clamp-4">{{ $item->description }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-2 mt-6 pt-3 border-t border-slate-900/5">
                            <button @click="currentInfo = {{ json_encode($item) }}; openEditModal = true" class="px-3 py-1.5 bg-white/80 hover:bg-white text-slate-700 rounded-lg text-xs font-medium transition cursor-pointer border border-slate-200 shadow-xs">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <form action="{{ route('kelola-konten.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus konten ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-medium transition cursor-pointer border border-red-100">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada konten informasi. Silakan tambah kartu baru.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>

    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div @click.away="openAddModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Tambah Informasi Baru</h2>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            
            <form action="{{ route('kelola-konten.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Kartu</label>
                        <input type="text" name="title" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50" placeholder="Masukkan judul info">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Informasi</label>
                        <textarea name="description" rows="4" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 resize-none" placeholder="Tulis rincian prosedur..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Ikon Informasi</label>
                        <select name="icon" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 cursor-pointer">
                            <option value="information-circle-outline">💡 Informasi Umum / Prosedur</option>
                            <option value="calendar-outline">📅 Jadwal & Agenda</option>
                            <option value="star-outline">⭐ Promo / Pengumuman Penting</option>
                            <option value="help-circle-outline">❓ Bantuan / Panduan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Unggah Gambar Sampul (Opsional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-1.5 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-1">* Format berkas: JPG, PNG, WEBP. Maksimal ukuran 2MB.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Warna Background</label>
                            <input type="color" name="color" value="#eff6ff" required class="w-full h-10 border border-gray-300 rounded-lg p-1 bg-gray-50 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Warna Teks & Icon</label>
                            <input type="color" name="text_color" value="#1e40af" required class="w-full h-10 border border-gray-300 rounded-lg p-1 bg-gray-50 cursor-pointer">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition shadow-sm">Simpan Konten</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div @click.away="openEditModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Edit Informasi</h2>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            
            <form :action="'{{ route('kelola-konten.index') }}/' + currentInfo.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Kartu</label>
                        <input type="text" name="title" :value="currentInfo.title" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Informasi</label>
                        <textarea name="description" :value="currentInfo.description" rows="4" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Ikon Informasi</label>
                        <select name="icon" x-model="currentInfo.icon" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2.5 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 cursor-pointer">
                            <option value="information-circle-outline">💡 Informasi Umum</option>
                            <option value="calendar-outline">📅 Jadwal & Agenda</option>
                            <option value="star-outline">⭐ Promo / Pengumuman Penting</option>
                            <option value="help-circle-outline">❓ Bantuan / Panduan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ganti Gambar (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-1.5 outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Warna Background</label>
                            <input type="color" name="color" :value="currentInfo.color" required class="w-full h-10 border border-gray-300 rounded-lg p-1 bg-gray-50 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Warna Teks</label>
                            <input type="color" name="text_color" :value="currentInfo.text_color" required class="w-full h-10 border border-gray-300 rounded-lg p-1 bg-gray-50 cursor-pointer">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition shadow-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style> [x-cloak] { display: none !important; } </style>
</body>
</html>