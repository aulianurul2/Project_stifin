<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">
            STIFIn Admin
        </div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 bg-blue-600">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>

            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>

            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-file-medical mr-3"></i> Hasil Tes
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-chart-bar mr-3"></i> Laporan
            </a>
            
            <div class="border-t border-slate-800 mt-4 pt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left py-3 px-6 text-red-400 hover:bg-red-900/20 transition">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">Dashboard</h2>
            <div class="text-sm text-gray-500">
                Login sebagai: <span class="font-bold text-gray-800">{{ Auth::user()->nama }}</span>
            </div>
        </header>

        <main class="p-6 h-full">
            <div class="bg-white border-2 border-dashed border-gray-200 rounded-lg h-full flex items-center justify-center">
                <div class="text-center">
                    <p class="text-gray-400">Area ini kosong. Silakan masukkan tabel atau form logic di sini.</p>
                </div>
            </div>
        </main>
    </div>

</body>
</html>