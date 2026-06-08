<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Manajemen Slot Jadwal - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <style>
        /* ── Utilities ── */
        .bg-info-light    { background-color: rgba(72, 171, 247, 0.1) !important; }
        .bg-primary-light { background-color: rgba(29, 124, 244, 0.1) !important; }
        @media (min-width: 768px) {
            .border-end-md   { border-right: 1px solid #ebedf2 !important; }
            .border-start-md { border-left:  1px solid #ebedf2 !important; }
        }
        .avatar-lg    { width: 48px !important; height: 48px !important; }
        .avatar-title { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
        tr.row-kedaluwarsa td { opacity: 0.6; }

        /* ── Modal Hapus (konsisten dengan kelola-konten) ── */
        #modalHapusJadwal .modal-content {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        #modalHapusJadwal .modal-hapus-header {
            background: linear-gradient(135deg, #ff4d4d 0%, #c0392b 100%);
            padding: 28px 28px 20px;
            text-align: center;
        }
        #modalHapusJadwal .modal-hapus-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            backdrop-filter: blur(4px);
        }
        #modalHapusJadwal .modal-hapus-icon i { font-size: 1.8rem; color: #fff; }
        #modalHapusJadwal .modal-hapus-title  { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0; }
        #modalHapusJadwal .modal-hapus-body   { padding: 24px 28px 8px; text-align: center; }
        #modalHapusJadwal .hapus-slot-name {
            display: inline-block;
            background: #fff4f4; border: 1px solid #ffd5d5; border-radius: 8px;
            padding: 6px 14px; font-weight: 600; color: #c0392b; font-size: 0.875rem;
            max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            margin-bottom: 12px;
        }
        #modalHapusJadwal .hapus-warning-text { font-size: 0.875rem; color: #6c757d; line-height: 1.6; margin: 0; }
        #modalHapusJadwal .modal-hapus-footer {
            padding: 16px 28px 24px;
            display: flex; gap: 10px; justify-content: center;
        }
        #modalHapusJadwal .btn-hapus-batal {
            flex: 1; max-width: 140px; padding: 10px 20px; border-radius: 10px;
            font-weight: 600; font-size: 0.875rem; border: 1.5px solid #dee2e6;
            background: #fff; color: #495057; transition: all 0.2s;
        }
        #modalHapusJadwal .btn-hapus-batal:hover { background: #f8f9fa; border-color: #adb5bd; }
        #modalHapusJadwal .btn-hapus-konfirmasi {
            flex: 1; max-width: 160px; padding: 10px 20px; border-radius: 10px;
            font-weight: 600; font-size: 0.875rem; border: none;
            background: linear-gradient(135deg, #ff4d4d 0%, #c0392b 100%);
            color: #fff; transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(192,57,43,0.3);
        }
        #modalHapusJadwal .btn-hapus-konfirmasi:hover  { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(192,57,43,0.4); }
        #modalHapusJadwal .btn-hapus-konfirmasi:active { transform: translateY(0); }

        /* ── Modal Detail Klien ── */
        #modalKlien .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,0.18);
        }

        /* Header gradient biru elegan */
        #modalKlien .modal-klien-header {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            padding: 28px 32px 24px;
            position: relative;
            overflow: hidden;
        }
        #modalKlien .modal-klien-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 180px; height: 180px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        #modalKlien .modal-klien-header::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -20px;
            width: 140px; height: 140px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        #modalKlien .modal-klien-header-inner {
            position: relative; z-index: 2;
            display: flex; align-items: center; justify-content: space-between;
        }
        #modalKlien .modal-klien-title-wrap {}
        #modalKlien .modal-klien-eyebrow {
            font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 4px;
        }
        #modalKlien .modal-klien-title {
            font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;
        }
        #modalKlien .modal-klien-meta {
            display: flex; gap: 20px; margin-top: 18px;
            position: relative; z-index: 2;
        }
        #modalKlien .modal-klien-meta-chip {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 24px;
            padding: 6px 14px;
            backdrop-filter: blur(6px);
        }
        #modalKlien .modal-klien-meta-chip i { color: rgba(255,255,255,0.8); font-size: 0.8rem; }
        #modalKlien .modal-klien-meta-chip span { color: #fff; font-size: 0.82rem; font-weight: 600; }

        /* Body */
        #modalKlien .modal-klien-body {
            padding: 0;
            max-height: 72vh;
            overflow-y: auto;
            background: #f4f6fb;
        }
        #modalKlien .modal-klien-body::-webkit-scrollbar { width: 5px; }
        #modalKlien .modal-klien-body::-webkit-scrollbar-track { background: #f4f6fb; }
        #modalKlien .modal-klien-body::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 10px; }

        #modalKlien .klien-section-label {
            font-size: 0.68rem; font-weight: 800; letter-spacing: 1.4px;
            text-transform: uppercase; color: #90a4ae;
            padding: 16px 24px 8px;
        }

        /* Kartu klien */
        .klien-card {
            margin: 0 16px 12px;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8eaf6;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .klien-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.09); }

        .klien-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px 12px;
            border-bottom: 1px solid #f0f2f8;
        }
        .klien-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(30,87,32,0.3);
        }
        .klien-name-wrap { margin-left: 14px; }
        .klien-name { font-size: 0.95rem; font-weight: 700; color: #1b5e20; margin: 0 0 3px; }

        .klien-card-body { padding: 14px 20px; }
        .klien-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .klien-info-item label {
            font-size: 0.67rem; font-weight: 800; letter-spacing: 1px;
            text-transform: uppercase; color: #90a4ae; display: block; margin-bottom: 3px;
        }
        .klien-info-item span { font-size: 0.84rem; font-weight: 600; color: #37474f; }
        .klien-info-item.full-width { grid-column: 1 / -1; }
        .klien-catatan {
            background: #f8f9ff; border-left: 3px solid #c5cae9;
            border-radius: 0 6px 6px 0; padding: 8px 12px;
            font-size: 0.82rem; color: #546e7a; line-height: 1.5; font-style: italic;
        }

        .klien-card-footer {
            padding: 12px 20px;
            background: #fafbff;
            border-top: 1px solid #f0f2f8;
            display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap;
        }

        /* Seksi bukti & biaya */
        .bukti-biaya-section {
            margin: 0 16px 16px;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8eaf6;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .bukti-biaya-tabs {
            display: flex;
            border-bottom: 1px solid #e8eaf6;
        }
        .bukti-biaya-tab {
            flex: 1; padding: 12px 16px; text-align: center;
            font-size: 0.78rem; font-weight: 700; letter-spacing: 0.5px;
            text-transform: uppercase; color: #90a4ae; cursor: default;
            border-bottom: 3px solid transparent;
        }
        .bukti-biaya-tab.active { color: #2e7d32; border-bottom-color: #2e7d32; }
        .bukti-biaya-content { padding: 16px 20px; }

        /* Biaya breakdown */
        .biaya-breakdown-v2 { width: 100%; }
        .biaya-row-v2 {
            display: flex; justify-content: space-between;
            padding: 7px 0; font-size: 0.84rem; color: #2e7d32;
            border-bottom: 1px dashed #eceff1;
        }
        .biaya-row-v2:last-child { border-bottom: none; }
        .biaya-total-v2 {
            display: flex; justify-content: space-between;
            padding: 10px 0 0; font-size: 0.95rem; font-weight: 800;
            color: #2e7d32; border-top: 2px solid #e8f5e9; margin-top: 4px;
        }
        .wilayah-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
            margin-bottom: 14px;
        }
        .wilayah-dalam { background: #e8f5e9; color: #2e7d32; }
        .wilayah-luar  { background: #e8f5e9; color: #2e7d32; }

        /* Bukti transfer */
        .bukti-thumb-v2 {
            border-radius: 10px; overflow: hidden;
            background: #f4f6f9; border: 1px solid #e0e0e0;
            display: flex; align-items: center; justify-content: center;
            min-height: 160px;
        }
        .bukti-thumb-v2 img {
            max-width: 100%; max-height: 200px; object-fit: contain;
            display: block; cursor: zoom-in;
            transition: transform 0.2s;
        }
        .bukti-thumb-v2 img:hover { transform: scale(1.02); }
        .bukti-empty-v2 {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 28px; color: #b0bec5;
        }
        .bukti-empty-v2 i { font-size: 2rem; margin-bottom: 8px; }
        .bukti-empty-v2 span { font-size: 0.8rem; text-align: center; }

        /* Footer modal */
        #modalKlien .modal-klien-footer {
            padding: 16px 24px;
            background: #fff;
            border-top: 1px solid #e8eaf6;
            display: flex; justify-content: flex-end;
        }
        #modalKlien .btn-tutup {
            padding: 10px 28px; border-radius: 10px;
            font-weight: 700; font-size: 0.875rem;
            border: 1.5px solid #dee2e6; background: #fff; color: #495057;
            transition: all 0.2s;
        }
        #modalKlien .btn-tutup:hover { background: #f8f9fa; border-color: #adb5bd; }

        /* Spinner */
        .klien-loading {
            padding: 40px; text-align: center; color: #90a4ae;
        }
        .klien-loading i { font-size: 1.8rem; display: block; margin-bottom: 10px; }
        .klien-loading span { font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="wrapper">

        @include('partials.sidebar')

        <div class="main-panel">
            <div class="main-header">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">

                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="fw-bold mb-3">Manajemen Slot Jadwal Tes</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="{{ route('jadwal-tes') }}">Jadwal Tes</a></li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <span class="badge badge-primary px-3 py-2 fs-7 btn-round fw-normal">
                                <i class="fas fa-calendar-check me-1"></i> {{ count($jadwal) }} Slot Diterbitkan
                            </span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show card-round" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form Terbitkan Slot Baru --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-header py-3">
                                    <div class="card-title text-uppercase fs-7 fw-bold text-muted">
                                        <i class="fas fa-plus-circle text-primary me-2"></i> Terbitkan Slot Baru
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('jadwal.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="kuota" value="1">
                                        <div class="row row-demo-grid align-items-end">
                                            <div class="col-sm-6 col-md-4 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Tanggal</label>
                                                    <input type="date" name="tanggal" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Waktu</label>
                                                    <input type="time" name="waktu" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-3 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Lokasi</label>
                                                    <select name="lokasi" class="form-select form-control">
                                                        <option value="Kantor Subang">Kantor Subang</option>
                                                        <option value="Home Visit">Home Visit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-2">
                                                <button type="submit" class="btn btn-primary btn-round w-100 fw-bold px-4">
                                                    <i class="fas fa-paper-plane me-1"></i> Terbitkan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Jadwal --}}
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table id="tabel-jadwal" class="table table-striped table-hover mb-0 align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Waktu</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Lokasi</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Status Slot</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Pendaftar</th>
                                                    <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwal as $item)
                                                @php
                                                    $jadwalDateTime = \Carbon\Carbon::parse($item->tanggal . ' ' . $item->waktu);
                                                    $sudahLewat = $jadwalDateTime->isPast() && empty($item->nama_klien);
                                                @endphp
                                                <tr class="{{ $sudahLewat ? 'row-kedaluwarsa' : '' }}">
                                                    <td class="px-4 py-3">
                                                        <div class="fw-bold text-dark fs-6">
                                                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                                                            @if($sudahLewat)
                                                                <i class="fas fa-history text-muted ms-1 fs-8" title="Jadwal sudah lewat"></i>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($item->lokasi == 'Home Visit')
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-home me-1"></i> Home Visit
                                                            </span>
                                                        @else
                                                            <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-building me-1"></i> Kantor Subang
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($sudahLewat)
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-clock me-1"></i> Kedaluwarsa
                                                            </span>
                                                        @elseif(!empty($item->nama_klien))
                                                            @php
                                                                $statusRaw = strtolower($item->status);
                                                                $badgeClass = 'badge-warning';
                                                                if(in_array($statusRaw, ['selesai', 'disetujui', 'diterima', 'sukses'])) $badgeClass = 'badge-success';
                                                                elseif(in_array($statusRaw, ['batal', 'ditolak'])) $badgeClass = 'badge-danger';
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }} px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                {{ in_array($statusRaw, ['menunggu','konfirmasi']) ? 'Menunggu' : ucfirst($item->status) }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-primary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">Tersedia</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if(!empty($item->nama_klien))
                                                            <button type="button"
                                                                    class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center btn-lihat-klien"
                                                                    style="background-color: #d1e7dd; border-color: #badbcc; min-width: 56px;"
                                                                    data-id="{{ $item->id_jadwal }}"
                                                                    data-waktu="{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB"
                                                                    data-lokasi="{{ $item->lokasi }}"
                                                                    data-bukti="{{ $item->bukti_transfer ?? '' }}"
                                                                    data-is-luar="{{ $item->is_luar_subang ? '1' : '0' }}"
                                                                    data-nama-kota="{{ $item->nama_kota ?? '' }}"
                                                                    data-biaya="{{ $item->biaya ?? 550000 }}"
                                                                    title="Lihat Detail Klien">
                                                                <i class="fas fa-user mb-1" style="color: #0f5132;"></i>
                                                                <span style="font-size: 9px; color: #0f5132; font-weight: 600;">Detail Klien</span>
                                                            </button>
                                                        @elseif($sudahLewat)
                                                            <span class="text-muted italic fs-8">
                                                                <i class="fas fa-minus-circle me-1"></i>Tidak ada pendaftar
                                                            </span>
                                                        @else
                                                            <span class="text-muted italic fs-8">Belum ada pemesan</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        {{-- Form hapus tersembunyi — di-submit oleh modal konfirmasi --}}
                                                        <form id="form-hapus-{{ $item->id_jadwal }}" action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        {{-- Tombol Hapus → trigger modal, bukan submit langsung --}}
                                                        <button type="button"
                                                                class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center mx-auto btn-hapus-jadwal"
                                                                style="background-color: #f8d7da; border-color: #f5c2c7; min-width: 56px;"
                                                                data-id="{{ $item->id_jadwal }}"
                                                                data-waktu="{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}, {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB"
                                                                title="Hapus Slot">
                                                            <i class="fas fa-trash-alt mb-1" style="color: #842029;"></i>
                                                            <span style="font-size: 9px; color: #842029; font-weight: 600;">Hapus</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>


    {{-- ══════════════════════════════════════
         MODAL KONFIRMASI HAPUS SLOT
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="modalHapusJadwal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content">
                <div class="modal-hapus-header">
                    <div class="modal-hapus-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <p class="modal-hapus-title">Hapus Slot Jadwal?</p>
                </div>
                <div class="modal-hapus-body">
                    <span class="hapus-slot-name" id="hapus_slot_waktu">—</span>
                    <p class="hapus-warning-text">
                        Slot jadwal ini akan dihapus secara permanen dan <strong>tidak dapat dikembalikan</strong>.
                        Apakah Anda yakin ingin melanjutkan?
                    </p>
                </div>
                <div class="modal-hapus-footer">
                    <button type="button" class="btn-hapus-batal" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn-hapus-konfirmasi" id="btn-konfirmasi-hapus-jadwal">
                        <i class="fas fa-trash-alt me-1"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════
         MODAL DETAIL KLIEN (dipercantik)
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="modalKlien" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                {{-- Header biru bergradient --}}
                <div class="modal-klien-header">
                    <div class="modal-klien-header-inner">
                        <div class="modal-klien-title-wrap">
                            <p class="modal-klien-eyebrow"><i class="fas fa-id-badge me-1"></i> Informasi Pendaftar</p>
                            <h5 class="modal-klien-title">Detail Klien</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.8;"></button>
                    </div>
                    <div class="modal-klien-meta">
                        <div class="modal-klien-meta-chip">
                            <i class="fas fa-calendar-alt"></i>
                            <span id="modal-waktu-jadwal">-</span>
                        </div>
                        <div class="modal-klien-meta-chip">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="modal-lokasi-jadwal">-</span>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-klien-body">

                    {{-- List klien (diisi via AJAX) --}}
                    <p class="klien-section-label"><i class="fas fa-users me-1"></i> Data Pendaftar</p>
                    <div id="container-list-klien">
                        <div class="klien-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memuat data pendaftar...</span>
                        </div>
                    </div>

                    {{-- Seksi Bukti Transfer & Rincian Biaya --}}
                    <p class="klien-section-label"><i class="fas fa-file-invoice-dollar me-1"></i> Pembayaran & Bukti Transfer</p>
                    <div class="bukti-biaya-section">
                        <div class="bukti-biaya-tabs">
                            <div class="bukti-biaya-tab active"><i class="fas fa-coins me-1"></i> Rincian Biaya</div>
                            <div class="bukti-biaya-tab active" style="border-left: 1px solid #e8eaf6;"><i class="fas fa-receipt me-1"></i> Bukti Transfer</div>
                        </div>
                        <div class="bukti-biaya-content">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div id="bukti-wilayah-wrap" class="mb-2"></div>
                                    <div id="bukti-biaya-breakdown"></div>
                                </div>
                                <div class="col-md-7">
                                    <div id="bukti-img-wrap" class="bukti-thumb-v2"></div>
                                    <div id="bukti-link-wrap" class="mt-2 d-none">
                                        <a id="bukti-full-link" href="#" target="_blank" class="btn btn-sm btn-outline-primary btn-round">
                                            <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 16px;"></div>
                </div>

                {{-- Footer --}}
                <div class="modal-klien-footer">
                    {{-- Form update status tersembunyi (dipakai AJAX, bukan submit biasa) --}}
                    <form id="formUpdateStatus" method="POST" style="display:none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" id="inputStatus">
                    </form>
                    <button type="button" class="btn-tutup" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>


    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        // ── Konstanta biaya ──
        const BIAYA_TES            = 550000;
        const BIAYA_TRANSPORT_LUAR = 75000;
        const TRANSPORT_DALAM = {
            'Kota Subang': 25000,
            'Kab. Subang': 50000,
        };

        function formatRupiah(nominal) {
            return 'Rp ' + parseInt(nominal).toLocaleString('id-ID');
        }

        function renderBiayaBreakdown(isLuar, namaKota) {
            var kotaBersih  = (namaKota || '').trim();
            var transport   = 0;
            var areaLabel   = '';
            var wilayahHTML = '';

            if (isLuar == '1') {
                transport   = BIAYA_TRANSPORT_LUAR;
                wilayahHTML = '<span class="wilayah-pill wilayah-luar"><i class="fas fa-road"></i>Luar Subang</span>';
            } else {
                transport   = TRANSPORT_DALAM[kotaBersih] !== undefined ? TRANSPORT_DALAM[kotaBersih] : 0;
                wilayahHTML = '<span class="wilayah-pill wilayah-dalam"><i class="fas fa-map-marker-alt"></i>Dalam Subang</span>';
                if (kotaBersih !== '' && TRANSPORT_DALAM[kotaBersih] !== undefined) areaLabel = kotaBersih;
            }

            var total = BIAYA_TES + transport;

            $('#bukti-wilayah-wrap').html(wilayahHTML);
            $('#bukti-biaya-breakdown').html(
                '<table class="biaya-breakdown-v2">' +
                '<tr class="biaya-row-v2"><td>Biaya Tes</td><td class="text-end fw-semibold">' + formatRupiah(BIAYA_TES) + '</td></tr>' +
                '<tr class="biaya-row-v2"><td>Biaya Transport' + (areaLabel ? ' <small class="text-muted">(' + areaLabel + ')</small>' : '') + '</td><td class="text-end fw-semibold">' + formatRupiah(transport) + '</td></tr>' +
                '</table>' +
                '<div class="biaya-total-v2"><span>Total Pembayaran</span><span>' + formatRupiah(total) + '</span></div>'
            );
        }

        $(document).ready(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#tabel-jadwal').DataTable({
                pageLength: 10,
                order: [],
                language: {
                    search: "Cari Slot:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Tidak ada data slot jadwal yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                }
            });

            // ── Buka modal hapus jadwal ──
            var targetHapusId = null;
            $('#tabel-jadwal').on('click', '.btn-hapus-jadwal', function () {
                targetHapusId = $(this).data('id');
                var waktu     = $(this).data('waktu');
                $('#hapus_slot_waktu').text(waktu);
                $('#modalHapusJadwal').modal('show');
            });

            // ── Konfirmasi hapus → submit form ──
            $('#btn-konfirmasi-hapus-jadwal').on('click', function () {
                if (targetHapusId) {
                    $('#modalHapusJadwal').modal('hide');
                    $('#form-hapus-' + targetHapusId).submit();
                }
            });

            // ── Buka modal detail klien ──
            $('#tabel-jadwal').on('click', '.btn-lihat-klien', function () {
                var idJadwal   = $(this).data('id');
                var infoWaktu  = $(this).data('waktu');
                var infoLokasi = $(this).data('lokasi');
                var bukti      = $(this).data('bukti');
                var isLuar     = $(this).data('is-luar');
                var namaKota   = $(this).data('nama-kota') || '';

                $('#modal-waktu-jadwal').text(infoWaktu);
                $('#modal-lokasi-jadwal').text(infoLokasi);

                renderBiayaBreakdown(isLuar, namaKota);

                // Render bukti transfer
                var imgWrap  = $('#bukti-img-wrap');
                var linkWrap = $('#bukti-link-wrap');

                if (bukti && bukti.trim() !== '') {
                    var imgUrl = '/uploads/bukti/' + bukti;
                    imgWrap.html(
                        '<img src="' + imgUrl + '" alt="Bukti Transfer"' +
                        ' onclick="window.open(\'' + imgUrl + '\', \'_blank\')"' +
                        ' onerror="this.parentElement.innerHTML=\'<div class=\\\'bukti-empty-v2\\\'><i class=\\\'fas fa-image\\\'></i><span>Gambar tidak dapat dimuat</span></div>\'">'
                    );
                    $('#bukti-full-link').attr('href', imgUrl);
                    linkWrap.removeClass('d-none');
                } else {
                    imgWrap.html(
                        '<div class="bukti-empty-v2">' +
                        '<i class="fas fa-file-image"></i>' +
                        '<span>Belum ada bukti transfer yang diupload</span>' +
                        '</div>'
                    );
                    linkWrap.addClass('d-none');
                }

                // Muat data klien via AJAX
                $('#container-list-klien').html(
                    '<div class="klien-loading">' +
                    '<i class="fas fa-spinner fa-spin"></i><span>Memuat data pendaftar...</span></div>'
                );
                $('#modalKlien').modal('show');

                $.ajax({
                    url: '/jadwal-tes/' + idJadwal + '/klien',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#container-list-klien').empty();

                        if (data.length === 0) {
                            $('#container-list-klien').html(
                                '<div class="klien-loading text-muted">' +
                                '<i class="fas fa-user-slash mb-2" style="font-size:1.8rem;"></i>' +
                                '<span>Belum ada klien yang memesan slot ini.</span></div>'
                            );
                            return;
                        }

                        $.each(data, function (index, item) {
                            var statusLower   = item.status_jadwal.toLowerCase();
                            var badgeClass    = 'badge-warning';
                            var displayStatus = item.status_jadwal;

                            if (['selesai','disetujui','diterima','sukses'].includes(statusLower)) badgeClass = 'badge-success';
                            else if (['batal','ditolak'].includes(statusLower)) badgeClass = 'badge-danger';

                            displayStatus = (statusLower === 'menunggu' || statusLower === 'konfirmasi')
                                ? 'Menunggu Konfirmasi'
                                : displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1).toLowerCase();

                            // Format nomor WA
                            var cleanPhone = '';
                            if (item.no_hp) {
                                cleanPhone = item.no_hp.replace(/[^0-9]/g, '');
                                if (cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.substring(1);
                                else if (!cleanPhone.startsWith('62')) cleanPhone = '62' + cleanPhone;
                            }

                            var waButton = item.no_hp
                                ? '<a href="https://wa.me/' + cleanPhone + '" target="_blank" class="btn btn-sm btn-success btn-round px-3 fw-semibold"><i class="fab fa-whatsapp me-1"></i>WhatsApp</a>'
                                : '<span class="text-muted fs-8">No. HP tidak tersedia</span>';

                            var aksiHtml = '';
                            if (statusLower === 'menunggu') {
                                aksiHtml = '<button data-id="' + item.id_jadwal + '" data-status="Ditolak" class="btn btn-sm btn-outline-danger btn-round px-3 fw-semibold btn-aksi-status"><i class="fas fa-times-circle me-1"></i>Tolak</button>';
                            } else if (statusLower === 'ditolak') {
                                aksiHtml = '<button data-id="' + item.id_jadwal + '" data-status="Tersedia" class="btn btn-sm btn-outline-success btn-round px-3 fw-semibold btn-aksi-status"><i class="fas fa-sync-alt me-1"></i>Buka Slot</button>';
                            } else {
                                aksiHtml = '<span class="badge bg-light text-muted border px-3 py-1 btn-round fs-8 fw-normal"><i class="fas fa-lock me-1"></i>Terkunci</span>';
                            }

                            var initials = item.nama_klien.charAt(0).toUpperCase();

                            var card = `
                                <div class="klien-card">
                                    <div class="klien-card-header">
                                        <div class="d-flex align-items-center">
                                            <div class="klien-avatar">${initials}</div>
                                            <div class="klien-name-wrap">
                                                <p class="klien-name">${item.nama_klien}</p>
                                                <span class="badge ${badgeClass} px-2 py-1 btn-round fs-8 fw-semibold">${displayStatus}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="klien-card-body">
                                        <div class="klien-info-grid">
                                            <div class="klien-info-item">
                                                <label>Nomor HP</label>
                                                <span>${item.no_hp ? item.no_hp : '—'}</span>
                                            </div>
                                            <div class="klien-info-item">
                                                <label>Status Booking</label>
                                                <span>${displayStatus}</span>
                                            </div>
                                            <div class="klien-info-item full-width">
                                                <label>Catatan Tambahan</label>
                                                <div class="klien-catatan">${item.komentar ? item.komentar : 'Tidak ada catatan khusus.'}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="klien-card-footer">
                                        ${waButton}
                                        ${aksiHtml}
                                    </div>
                                </div>`;
                            $('#container-list-klien').append(card);
                        });
                    },
                    error: function () {
                        $('#container-list-klien').html(
                            '<div class="klien-loading text-danger">' +
                            '<i class="fas fa-exclamation-triangle mb-2" style="font-size:1.8rem;"></i>' +
                            '<span>Gagal mengambil data pendaftar. Coba refresh halaman.</span></div>'
                        );
                    }
                });
            });

            // ── Tolak / Buka Kembali dari dalam modal klien ──
            $('#container-list-klien').on('click', '.btn-aksi-status', function () {
                var idJadwal     = $(this).data('id');
                var statusTarget = $(this).data('status');
                executeUpdateStatus(idJadwal, statusTarget);
            });
        });

        function executeUpdateStatus(id, status) {
            if (!confirm(status === 'Ditolak' ? 'Tolak permohonan klien ini?' : 'Buka kembali slot ini?')) return;
            $.ajax({
                url: '/jadwal-tes/' + id + '/update-status',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PUT', status: status },
                success: function () { location.reload(); },
                error: function (xhr) {
                    alert('Terjadi kesalahan saat memperbarui status.');
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

    <script>
        // Set tanggal minimum = hari ini
        const inputTanggal = document.querySelector('input[name="tanggal"]');
        if (inputTanggal) {
            const today = new Date().toISOString().split('T')[0];
            inputTanggal.setAttribute('min', today);
        }
    </script>
</body>
</html>