<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pendaftaran Tes - STIFIn Admin</title>
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
        /* Mengatur form di dalam modal agar seragam, bersih, dan tidak merusak layout luar */
        .modal-content .form-control, 
        .modal-content .form-select {
            border: 1px solid #ced4da !important;
            color: #495057 !important;
        }
        .modal-content .form-control:focus, 
        .modal-content .form-select:focus {
            border-color: #6c757d !important;
            box-shadow: none !important;
        }
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
                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="fw-bold mb-3">Pendaftaran Tes</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Pendaftaran Tes</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Pendaftaran Tes STIFIn</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>No. HP</th>
                                                    <th>Jadwal Tes</th>
                                                    <th>Lokasi</th>
                                                    <th>Status</th>
                                                    <th>Komentar</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendaftaran as $item)
                                                <tr>
                                                    <td>{{ $item->nama_klien }}</td>
                                                    <td>{{ $item->no_hp }}</td>
                                                    <td>
                                                        <span class="d-block fw-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                                                    </td>
                                                    <td>
                                                        @if($item->lokasi == 'Home Visit')
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                                                                <i class="fas fa-home me-1"></i> Home Visit
                                                            </span>
                                                        @else
                                                            <span class="badge badge-info px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                                                                <i class="fas fa-building me-1"></i> Kantor Cabang
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->status == 'Diterima')
                                                            <span class="badge badge-success">Diterima</span>
                                                        @elseif($item->status == 'Ditolak')
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge badge-warning text-white">Menunggu</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        {{ $item->komentar ?? 'Tidak ada komen' }}
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" 
                                                                    class="btn btn-link btn-primary btn-lg"
                                                                    onclick="openModalView('{{ $item->nama_klien }}', '{{ $item->no_hp }}', '{{ $item->email }}', '{{ $item->tanggal_lahir }}', '{{ $item->jenis_kelamin }}', '{{ $item->golongan_darah }}', '{{ $item->institusi ?? '-' }}', '{{ $item->sosmed ?? '-' }}', '{{ $item->domisili ?? '-' }}', '{{ $item->alamat }}')">
                                                                <i class="fa fa-eye"></i>
                                                            </button>

                                                            <button type="button" 
                                                                    class="btn btn-link btn-primary btn-lg"
                                                                    onclick="openModalStatus('{{ $item->id_jadwal }}', '{{ $item->nama_klien }}', '{{ ($item->status == 'Diterima' || $item->status == 'Ditolak') ? $item->status : 'Menunggu' }}', '{{ $item->komentar ?? '' }}')">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted italic">Belum ada data pendaftaran.</td>
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

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright text-center w-100">
                        2026, made with <i class="fa fa-heart heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- MODAL DETAIL PROFIL KLIEN (VIEW MODE - CLEAN & FORMAL) --}}
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="modalViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg" style="border-radius: 4px; border: 1px solid #dcdcdc;">
                
                <div class="modal-header d-flex justify-content-between align-items-center py-3 border-bottom bg-white">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalViewLabel" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                        DETAIL PENDAFTARAN KLIEN
                    </h5>
                    <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        
                        <div class="col-12 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">I. IDENTITAS UTAMA</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Nama Lengkap</label>
                            <span id="viewNama" class="text-dark fw-bold"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">No. HP / WhatsApp</label>
                            <span id="viewNoHp" class="text-dark fw-bold"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Alamat Email</label>
                            <span id="viewEmail" class="text-dark"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">II. INFORMASI PERSONAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Tanggal Lahir</label>
                            <span id="viewTglLahir" class="text-dark"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Golongan Darah / Jenis Kelamin</label>
                            <span id="viewGoldarJK" class="text-dark text-uppercase"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">III. AFILIASI & MEDIA SOSIAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Institusi / Perusahaan</label>
                            <span id="viewInstitusi" class="text-dark"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Media Sosial</label>
                            <span id="viewSosmed" class="text-dark"></span>
                        </div>

                        <div class="col-12 mt-2 mb-3">
                            <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">IV. LOKASI TEMPAT TINGGAL</span>
                            <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block mb-1">Domisili (Kota / Kabupaten)</label>
                            <span id="viewDomisili" class="text-dark"></span>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="text-muted small d-block mb-1">Alamat Lengkap</label>
                            <div id="viewAlamat" class="text-dark" style="line-height: 1.6; white-space: pre-line; font-size: 0.9rem;"></div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer bg-white d-flex justify-content-end py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 3px; font-weight: 600;">
                        TUTUP
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT STATUS (EDIT MODE - CLEAN & PROFESSIONAL) --}}
    <div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 4px; border: 1px solid #dcdcdc;">
                
                <div class="modal-header d-flex justify-content-between align-items-center py-3 border-bottom bg-white">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalStatusLabel" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                        PERBARUI STATUS PENDAFTARAN
                    </h5>
                    <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1;">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="formStatus" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-body p-4 bg-white">
                        <div class="row">
                            
                            <div class="col-12 mb-3">
                                <span class="fw-bold small text-secondary d-block mb-1" style="letter-spacing: 0.5px;">IDENTITAS KLIEN: <span id="modalNama" class="text-dark"></span></span>
                                <div style="height: 1px; background-color: #e0e0e0; width: 100%;"></div>
                            </div>

                            <div class="form-group col-md-12 mb-3 p-0">
                                <label class="text-muted small mb-1 fw-semibold">Pilih Status</label>
                                <select name="status" id="inputStatus" class="form-control form-select" style="border-radius: 3px;" required>
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-12 mb-2 p-0">
                                <label class="text-muted small mb-1 fw-semibold">Komentar / Catatan *</label>
                                <textarea name="komentar" id="inputKomentar" class="form-control" rows="4" style="border-radius: 3px; resize: none; line-height: 1.5;" placeholder="Alasan ditolak atau informasi tambahan jadwal..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-end py-3 bg-white border-top" style="padding-right: 1.5rem;">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-dismiss="modal" style="border-radius: 3px; font-weight: 600;">
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

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Re-inisialisasi agar fungsi pencarian, sorting, dan filter bawaan tabel aktif kembali
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

        const modalViewBS = new bootstrap.Modal(document.getElementById('modalView'));
        const modalStatusBS = new bootstrap.Modal(document.getElementById('modalStatus'));

        function openModalView(nama, noHp, email, tglLahir, jk, goldar, institusi, sosmed, domisili, alamat) {
            const jkText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : jk);
            
            document.getElementById('viewNama').innerText = nama;
            document.getElementById('viewNoHp').innerText = noHp;
            document.getElementById('viewEmail').innerText = email;
            document.getElementById('viewTglLahir').innerText = tglLahir;
            document.getElementById('viewGoldarJK').innerText = `${goldar} / ${jkText}`;
            document.getElementById('viewInstitusi').innerText = institusi;
            document.getElementById('viewSosmed').innerText = sosmed;
            document.getElementById('viewDomisili').innerText = domisili;
            document.getElementById('viewAlamat').innerText = alamat;

            modalViewBS.show();
        }

        function openModalStatus(id, nama, status, komentar) {
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('inputStatus').value = status;
            document.getElementById('inputKomentar').value = komentar;

            const baseUrl = "{{ url('pendaftaran-tes') }}";
            document.getElementById('formStatus').action = `${baseUrl}/${id}`;

            modalStatusBS.show();
        }
    </script>
</body>
</html>