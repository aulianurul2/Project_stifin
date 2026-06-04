<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Hasil Tes - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link class="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () { sessionStorage.fonts = true; },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
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
                            <h3 class="fw-bold mb-3">Manajemen Hasil Tes</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#" class="text-muted">Hasil Tes</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show card-round shadow-sm mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show card-round shadow-sm mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs nav-line nav-color-primary border-bottom" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 fw-bold {{ $tab == 'kelola' ? 'active' : '' }}" href="?tab=kelola">
                                        <i class="fas fa-tasks me-2"></i> Kelola Hasil Tes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 fw-bold {{ $tab == 'riwayat' ? 'active' : '' }}" href="?tab=riwayat">
                                        <i class="fas fa-history me-2"></i> Riwayat Tes
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        {{--
                                            Tab 'kelola' : DataTable (client-side, pakai get())
                                            Tab 'riwayat': tabel biasa + server-side pagination (pakai paginate())
                                            Keduanya pakai tabel terpisah supaya DataTable tidak bentrok dengan paginator Laravel.
                                        --}}
                                        @if($tab == 'kelola')
                                            <table id="basic-datatables" class="table table-striped table-hover mb-0 align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 40%">Nama Klien</th>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Status</th>
                                                        <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data as $item)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <div class="fw-bold text-dark fs-6">{{ $item->nama }}</div>
                                                                <small class="text-muted"><i class="fab fa-whatsapp me-1"></i> {{ $item->no_hp }}</small>
                                                            </td>
                                                            <td class="px-4 py-3">
                                                                <span class="badge badge-primary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                    Belum diinput
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                {{--
                                                                    Kirim id_tes (untuk update hasiltes) DAN id_jadwal
                                                                    (untuk query tabel jadwal di controller).
                                                                    openModal menerima keduanya.
                                                                --}}
                                                                <button type="button"
                                                                    onclick="openModal('{{ $item->id_tes }}', '{{ $item->id_jadwal }}', '{{ $item->nama }}')"
                                                                    class="btn btn-primary btn-sm btn-round px-3">
                                                                    <i class="fas fa-check-circle me-1"></i> Input Hasil
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center py-4 text-muted">
                                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                                Tidak ada data yang perlu dikelola.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                        @else
                                            {{-- Riwayat: server-side pagination, tidak pakai DataTable JS --}}
                                            <table class="table table-striped table-hover mb-0 align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 40%">Nama Klien</th>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Hasil</th>
                                                        <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data as $item)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <div class="fw-bold text-dark fs-6">{{ $item->nama }}</div>
                                                                <small class="text-muted"><i class="fab fa-whatsapp me-1"></i> {{ $item->no_hp }}</small>
                                                            </td>
                                                            <td class="px-4 py-3">
                                                                <span class="badge badge-success px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                    <i class="fas fa-check-circle me-1"></i> Tersertifikasi
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                                    <a href="{{ asset('uploads/hasil/' . $item->file_hasil) }}"
                                                                       download="{{ 'Sertifikat_' . $item->nama }}"
                                                                       class="btn btn-sm p-2 text-dark card-round shadow-sm"
                                                                       style="background-color: #fff3cd; border-color: #ffeeba;"
                                                                       title="Download Sertifikat">
                                                                        <i class="fas fa-file-download text-warning"></i>
                                                                    </a>

                                                                    <a href="{{ asset('uploads/hasil/' . $item->file_detail) }}"
                                                                       download="{{ 'Detail_Hasil_' . $item->nama }}"
                                                                       class="btn btn-sm p-2 card-round shadow-sm"
                                                                       style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;"
                                                                       title="Download Hasil Lengkap">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>

                                                                    <button type="button"
                                                                            onclick="previewFile('{{ asset('uploads/hasil/' . $item->file_hasil) }}')"
                                                                            class="btn btn-sm p-2 card-round shadow-sm"
                                                                            style="background-color: #586983; border-color: #636d7c; color: #ffffff;"
                                                                            title="Preview Sertifikat">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                     <button type="button"
                onclick="openEditModal('{{ $item->id_tes }}', '{{ $item->nama }}')"
                class="btn btn-sm p-2 card-round shadow-sm"
                style="background-color: #cfe2ff; border-color: #b6d4fe; color: #084298;"
                title="Ganti File">
            <i class="fas fa-pencil-alt"></i>
        </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center py-4 text-muted">
                                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                                Belum ada riwayat tes yang selesai.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                            {{-- Pagination hanya untuk tab riwayat --}}
                                            @if($data->hasPages())
                                                <div class="d-flex justify-content-end mt-3">
                                                    {{ $data->links() }}
                                                </div>
                                            @endif
                                        @endif
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
                        2026, made with <i class="fa fa-heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Modal Input Hasil --}}
    <div class="modal fade" id="modalHasil" tabindex="-1" aria-labelledby="modalHasilLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-round p-2">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalHasilLabel">
                        Input Berkas Hasil: <span id="modalNama" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{--
                    Action digenerate dari route hasil.update dengan :id_tes,
                    tapi controller butuh id_jadwal untuk query tabel jadwal.
                    Solusi: kirim id_jadwal sebagai hidden input, route tetap pakai id_tes
                    agar update hasiltes masih menggunakan id_tes yang benar.
                --}}
                <form id="formHasil" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="id_jadwal" id="hiddenIdJadwal">
                    <div class="modal-body">
                        <div class="form-group p-0 mb-3">
                            <label class="fw-bold small mb-1 text-dark">
                                <i class="fas fa-certificate text-warning me-2"></i>Unggah Sertifikat (Ringkasan)
                            </label>
                            <input type="file" name="file_hasil" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF / JPG / PNG</small>
                        </div>

                        <div class="form-group p-0 mb-2">
                            <label class="fw-bold small mb-1 text-dark">
                                <i class="fas fa-file-alt text-primary me-2"></i>Unggah Hasil Tes Lengkap (Detail)
                            </label>
                            <input type="file" name="file_detail" class="form-control" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF / DOC / DOCX</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm fw-bold px-3 btn-round" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 btn-round shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Preview Sertifikat --}}
    <div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content card-round overflow-hidden" style="max-height: 95vh;">
                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-light rounded text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark" id="modalPreviewLabel">Preview Sertifikat Hasil Tes</h5>
                    </div>
                    <button type="button" class="btn-close fs-4 px-2" onclick="closePreview()" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 65vh; min-height: 500px;">
                    <iframe id="previewFrame" src="" class="w-100 h-100 border-0 m-0 p-0"></iframe>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-3">
                    <span class="text-muted small fst-italic">*Gunakan tombol unduh di tabel jika ingin menyimpan file.</span>
                    <button type="button" onclick="closePreview()" class="btn btn-dark btn-round px-4 fw-bold shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit File --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-round p-2">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalEditLabel">
                    <i class="fas fa-pencil-alt text-primary me-2"></i>
                    Ganti Berkas: <span id="editNama" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info alert-sm py-2 px-3 card-round" style="font-size: 12px;">
                        <i class="fas fa-info-circle me-1"></i>
                        Kosongkan field yang tidak ingin diganti. File lama akan otomatis terhapus.
                    </div>

                    <div class="form-group p-0 mb-3">
                        <label class="fw-bold small mb-1 text-dark">
                            <i class="fas fa-certificate text-warning me-2"></i>Ganti Sertifikat (Opsional)
                        </label>
                        <input type="file" name="file_hasil" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF / JPG / PNG</small>
                    </div>

                    <div class="form-group p-0 mb-2">
                        <label class="fw-bold small mb-1 text-dark">
                            <i class="fas fa-file-alt text-primary me-2"></i>Ganti Hasil Tes Lengkap (Opsional)
                        </label>
                        <input type="file" name="file_detail" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF / DOC / DOCX</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm fw-bold px-3 btn-round" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 btn-round shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
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
        $(document).ready(function () {
            // DataTable hanya diinisialisasi di tab kelola
            @if($tab == 'kelola')
            $('#basic-datatables').DataTable({
                "pageLength": 10,
                "order": [],
                "language": {
                    "search": "Cari Data:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data yang cocok",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Data tidak tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)"
                }
            });
            @endif
        });

        /**
         * Buka modal input hasil.
         * @param {string} idTes     — untuk di-set ke action form (update hasiltes)
         * @param {string} idJadwal  — untuk dikirim sebagai hidden input (query tabel jadwal)
         * @param {string} nama      — nama klien untuk ditampilkan di judul modal
         */
        function openModal(idTes, idJadwal, nama) {
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('hiddenIdJadwal').value = idJadwal;

            let url = "{{ route('hasil.update', ':id') }}";
            url = url.replace(':id', idTes);
            document.getElementById('formHasil').action = url;

            var modalInput = new bootstrap.Modal(document.getElementById('modalHasil'));
            modalInput.show();
        }

        function previewFile(url) {
            document.getElementById('previewFrame').src = url;
            var modalPreview = new bootstrap.Modal(document.getElementById('modalPreview'));
            modalPreview.show();
        }

        function closePreview() {
            var modalEl = document.getElementById('modalPreview');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
            document.getElementById('previewFrame').src = "";
        }
        function openEditModal(idTes, nama) {
    document.getElementById('editNama').innerText = nama;

    let url = "{{ route('hasil.edit', ':id') }}";
    url = url.replace(':id', idTes);
    document.getElementById('formEdit').action = url;

    var modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));
    modalEdit.show();
}
    </script>
</body>
</html>