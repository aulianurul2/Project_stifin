<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola Klien - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () { sessionStorage.fonts = true; },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <!-- Third Party Plugins (Alpine.js & SweetAlert2) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        .modal-backdrop-custom {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Hanya override form khusus di dalam modal agar tidak merusak elemen pencarian tabel luar */
        .modal-backdrop-custom .form-control,
        .modal-backdrop-custom .form-select {
            border: 1px solid #ced4da !important;
            color: #495057 !important;
        }
        .modal-backdrop-custom .form-control:focus,
        .modal-backdrop-custom .form-select:focus {
            border-color: #6c757d !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body>
    <div class="wrapper" x-data="{ openModal: false, openView: false, selected: {}, selectedView: {} }">

        @include('partials.sidebar')

        <div class="main-panel">
            <div class="main-header">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                        <h3 class="fw-bold mb-3">Kelola Klien</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="{{ route('kelola-klien') }}">Data Klien</a></li>
                        </ul>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Klien STIFIn</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>No. HP</th>
                                                    <th>Status</th>
                                                    <th class="text-center" style="width: 15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($klien as $k)
                                                <tr>
                                                    <td>{{ $k->nama }}</td>
                                                    <td>{{ $k->no_hp ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $status = $k->status_jadwal ?? 'Menunggu';
                                                            $badgeClass = 'badge-warning';
                                                            if(in_array($status, ['Diterima', 'Selesai'])) $badgeClass = 'badge-success';
                                                            if(in_array($status, ['Ditolak', 'Batal'])) $badgeClass = 'badge-danger';
                                                            if($status == 'Proses') $badgeClass = 'badge-info';
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-2">

                                                            {{-- Tombol Lihat Detail Profil --}}
                                                            <button type="button"
                                                                class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
                                                                style="background-color: #d1e7dd; border-color: #badbcc; min-width: 56px;"
                                                                title="Lihat Detail"
                                                                @click="selectedView = {
                                                                    nama: '{{ $k->nama }}',
                                                                    no_hp: '{{ $k->no_hp ?? '-' }}',
                                                                    tanggal_lahir: '{{ $k->tanggal_lahir ?? '-' }}',
                                                                    jenis_kelamin: '{{ $k->jenis_kelamin }}',
                                                                    jenis_kelamin_text: '{{ $k->jenis_kelamin == 'L' ? 'Laki-laki' : ($k->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}',
                                                                    golongan_darah: '{{ $k->golongan_darah ?? '-' }}',
                                                                    email: '{{ $k->email ?? '-' }}',
                                                                    alamat: '{{ $k->alamat ?? '-' }}',
                                                                    institusi: '{{ $k->institusi ?? '-' }}',
                                                                    sosmed: '{{ $k->sosmed ?? '-' }}',
                                                                    domisili: '{{ $k->domisili ?? '-' }}',
                                                                    status: '{{ $status }}'
                                                                }; openView = true">
                                                                <i class="fa fa-eye mb-1" style="color: #0f5132;"></i>
                                                                <span style="font-size: 9px; color: #0f5132; font-weight: 600;">Detail</span>
                                                            </button>

                                                            {{-- Tombol Edit Profil --}}
                                                            <button type="button"
                                                                class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
                                                                style="background-color: #cfe2ff; border-color: #b6d4fe; min-width: 56px;"
                                                                title="Edit Profil"
                                                                @click="selected = {
                                                                    id: '{{ $k->id_klien }}',
                                                                    nama: '{{ $k->nama }}',
                                                                    no_hp: '{{ $k->no_hp }}',
                                                                    email: '{{ $k->email }}',
                                                                    tanggal_lahir: '{{ $k->tanggal_lahir }}',
                                                                    jenis_kelamin: '{{ $k->jenis_kelamin }}',
                                                                    golongan_darah: '{{ $k->golongan_darah }}',
                                                                    institusi: '{{ $k->institusi }}',
                                                                    sosmed: '{{ $k->sosmed }}',
                                                                    domisili: '{{ $k->domisili }}',
                                                                    alamat: '{{ $k->alamat }}'
                                                                }; openModal = true">
                                                                <i class="fa fa-edit mb-1" style="color: #084298;"></i>
                                                                <span style="font-size: 9px; color: #084298; font-weight: 600;">Edit</span>
                                                            </button>

                                                            {{-- Form & Tombol Hapus Klien --}}
                                                            <form id="delete-form-{{ $k->id_klien }}" action="{{ route('klien.destroy', $k->id_klien) }}" method="POST" style="display:inline;">
                                                                @csrf @method('DELETE')
                                                                <button type="button"
                                                                    class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
                                                                    style="background-color: #f8d7da; border-color: #f5c2c7; min-width: 56px;"
                                                                    title="Hapus Klien"
                                                                    onclick="konfirmasiHapus('{{ $k->id_klien }}', '{{ $k->nama }}')">
                                                                    <i class="fas fa-trash-alt mb-1" style="color: #842029;"></i>
                                                                    <span style="font-size: 9px; color: #842029; font-weight: 600;">Hapus</span>
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted italic">Belum ada data klien.</td>
                                                </tr>
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

        {{-- MODAL DETAIL PROFIL KLIEN (VIEW MODE - CLEAN & FORMAL) --}}
        <div x-show="openView" x-cloak class="modal-backdrop-custom">
            <div class="card w-75 shadow-lg" style="max-width: 750px; border-radius: 4px; border: 1px solid #dcdcdc;" @click.away="openView = false">

                <div class="card-header d-flex justify-content-between align-items-center py-3 border-bottom bg-white">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                        DETAIL PROFIL KLIEN
                    </h5>
                    <button type="button" class="close text-muted" @click="openView = false" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">

                        <div class="col-12 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">I. IDENTITAS UTAMA</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Nama Lengkap</label>
                            <span class="text-dark fw-bold" x-text="selectedView.nama"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Status Akun</label>
                            <span class="text-dark fw-bold" x-text="selectedView.status"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">No. HP / WhatsApp</label>
                            <span class="text-dark" x-text="selectedView.no_hp"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Alamat Email</label>
                            <span class="text-dark" x-text="selectedView.email"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">II. INFORMASI PERSONAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block mb-1">Tanggal Lahir</label>
                            <span class="text-dark" x-text="selectedView.tanggal_lahir"></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block mb-1">Jenis Kelamin</label>
                            <span class="text-dark" x-text="selectedView.jenis_kelamin_text"></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block mb-1">Golongan Darah</label>
                            <span class="text-dark text-uppercase" x-text="selectedView.golongan_darah || '-'"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">III. AFILIASI & MEDIA SOSIAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Institusi / Perusahaan</label>
                            <span class="text-dark" x-text="selectedView.institusi"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Media Sosial</label>
                            <span class="text-dark" x-text="selectedView.sosmed"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">IV. LOKASI TEMPAT TINGGAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block mb-1">Domisili (Kota / Kabupaten)</label>
                            <span class="text-dark" x-text="selectedView.domisili"></span>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="text-muted small d-block mb-1">Alamat Lengkap</label>
                            <div class="text-dark" style="line-height: 1.6; white-space: pre-line; font-size: 0.9rem;" x-text="selectedView.alamat"></div>
                        </div>

                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-end py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="openView = false" style="border-radius: 3px; font-weight: 600;">
                        TUTUP
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT KLIEN (EDIT MODE - CLEAN & PROFESSIONAL) --}}
        <div x-show="openModal" x-cloak class="modal-backdrop-custom">
            <div class="card w-75 shadow-lg" style="max-width: 700px; border-radius: 4px; border: 1px solid #dcdcdc;" @click.away="openModal = false">

                <div class="card-header d-flex justify-content-between align-items-center py-3 border-bottom bg-white">
                    <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                        PERBARUI DATA KLIEN
                    </h5>
                    <button type="button" class="close text-muted" @click="openModal = false" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">
                        <span>&times;</span>
                    </button>
                </div>

                <form :action="'{{ url('kelola-klien') }}/' + selected.id" method="POST">
                    @csrf @method('PUT')

                    <div class="card-body p-4 bg-white" style="max-height: 65vh; overflow-y: auto;">
                        <div class="row">

                            <div class="col-12 mb-3">
                                <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">I. DATA IDENTITAS UTAMA</span>
                                <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Nama Lengkap</label>
                                <input type="text" name="nama" x-model="selected.nama" class="form-control" style="border-radius: 3px;" required>
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">No. HP / WhatsApp</label>
                                <input type="text" name="no_hp" x-model="selected.no_hp" class="form-control" style="border-radius: 3px;" required>
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Alamat Email</label>
                                <input type="email" name="email" x-model="selected.email" class="form-control" style="border-radius: 3px;">
                            </div>

                            <div class="col-12 mt-2 mb-3">
                                <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">II. INFORMASI PERSONAL</span>
                                <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" x-model="selected.tanggal_lahir" class="form-control" style="border-radius: 3px;">
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" x-model="selected.jenis_kelamin" class="form-control form-select" style="border-radius: 3px;">
                                    <option value="">- Pilih -</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Gol. Darah</label>
                                <select name="golongan_darah" x-model="selected.golongan_darah" class="form-control form-select" style="border-radius: 3px;">
                                    <option value="">-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>

                            <div class="col-12 mt-2 mb-3">
                                <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">III. AFILIASI & LOKASI TEMPAT TINGGAL</span>
                                <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Institusi / Perusahaan</label>
                                <input type="text" name="institusi" x-model="selected.institusi" class="form-control" style="border-radius: 3px;">
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Media Sosial (IG/FB)</label>
                                <input type="text" name="sosmed" x-model="selected.sosmed" class="form-control" style="border-radius: 3px;">
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label class="text-muted small mb-1 fw-semibold">Domisili (Kota / Kabupaten)</label>
                                <input type="text" name="domisili" x-model="selected.domisili" class="form-control" style="border-radius: 3px;">
                            </div>

                            <div class="form-group col-md-12 mb-2">
                                <label class="text-muted small mb-1 fw-semibold">Alamat Lengkap</label>
                                <textarea name="alamat" x-model="selected.alamat" class="form-control" rows="3" style="border-radius: 3px; resize: none; line-height: 1.5;"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-action d-flex justify-content-end py-3 bg-white border-top" style="padding-right: 1.5rem;">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" @click="openModal = false" style="border-radius: 3px; font-weight: 600;">
                            BATAL
                        </button>
                        <button type="submit" class="btn btn-dark btn-sm" style="border-radius: 3px; font-weight: 600; letter-spacing: 0.3px;">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Datatables Plugin & Inisialisasi bawaan KaiAdmin -->
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Re-inisialisasi agar fungsi pencarian dan filter bawaan tabel aktif kembali
            $('#add-row').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang cocok ditemukan",
                    "info":  "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Data tidak tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)"
                }
            });
        });

        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'KONFIRMASI PENGHAPUSAN',
                text: `Apakah Anda yakin ingin menghapus data klien "${nama}" secara permanen? Tindakan ini juga akan menghapus data akun terkait.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a202c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'YA, HAPUS',
                cancelButtonText: 'BATAL',
                customClass: {
                    popup: 'border-radius-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>