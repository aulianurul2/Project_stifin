<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Hasil Tes - STIFIn</title>
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
                            <h3 class="fw-bold mb-1">Manajemen Hasil Tes</h3>
                            <ul class="breadcrumbs mb-0 p-0" style="background: transparent;">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#" class="text-muted">Hasil Tes</a></li>
                            </ul>
                        </div>
                    </div>

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
                        <div class="col-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 text-nowrap">
                                            <thead class="bg-light text-secondary">
                                                <tr>
                                                    <th class="px-4 py-3 fw-bold text-uppercase small" style="width: 40%">Nama Klien</th>
                                                    <th class="px-4 py-3 fw-bold text-uppercase small" style="width: 30%">Hasil</th>
                                                    <th class="px-4 py-3 fw-bold text-uppercase small text-center" style="width: 30%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($data as $item)
                                                    <tr>
                                                        <td class="px-4 py-3">
                                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $item->nama }}</div>
                                                            <div class="text-muted small"><i class="fab fa-whatsapp me-1"></i> {{ $item->no_hp }}</div>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            @if($item->status_tes == 'Selesai')
                                                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 card-round">
                                                                    <i class="fas fa-check-circle me-1"></i> Tersertifikasi
                                                                </span>
                                                            @else
                                                                <span class="text-muted fst-italic small">Belum diinput</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 text-center">
                                                            @if($tab == 'kelola')
                                                                <button type="button" onclick="openModal('{{ $item->id_tes }}', '{{ $item->nama }}')" class="btn btn-primary btn-sm btn-round px-3">
                                                                    <i class="fas fa-check-circle me-1"></i> Input Hasil
                                                                </button>
                                                            @else
                                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                                    <a href="{{ asset('uploads/hasil/' . $item->file_hasil) }}"
                                                                       download="{{ 'Sertifikat_' . $item->nama }}"
                                                                       class="btn btn-warning btn-sm p-2 text-dark card-round shadow-sm"
                                                                       style="background-color: #fff3cd; border-color: #ffeeba;"
                                                                       title="Download Sertifikat">
                                                                        <i class="fas fa-certificate text-warning-700"></i>
                                                                    </a>

                                                                    <a href="{{ asset('uploads/hasil/' . $item->file_detail) }}"
                                                                       download="{{ 'Detail_Hasil_' . $item->nama }}"
                                                                       class="btn btn-success btn-sm p-2 text-success card-round shadow-sm"
                                                                       style="background-color: #d1e7dd; border-color: #badbcc;"
                                                                       title="Download Hasil Lengkap">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>

                                                                    <button type="button" onclick="previewFile('{{ asset('uploads/hasil/' . $item->file_hasil) }}')"
                                                                            class="btn btn-primary btn-sm p-2 text-primary card-round shadow-sm"
                                                                            style="background-color: #cfe2ff; border-color: #b6d4fe;"
                                                                            title="Preview Sertifikat">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-5 text-muted fst-italic">
                                                            <i class="fas fa-exclamation-circle d-block mb-2 style="font-size: 2rem;"></i>
                                                            Data tidak ditemukan
                                                        </td>
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
                        2026, made with <i class="fa fa-heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="modal fade" id="modalHasil" tabindex="-1" aria-labelledby="modalHasilLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-round p-2">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalHasilLabel">Input Berkas Hasil: <span id="modalNama" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHasil" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group p-0 mb-3">
                            <label class="fw-bold small mb-1 text-dark"><i class="fas fa-certificate text-warning me-2"></i>Unggah Sertifikat (Ringkasan)</label>
                            <input type="file" name="file_hasil" class="form-control" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF/JPG/PNG</small>
                        </div>

                        <div class="form-group p-0 mb-2">
                            <label class="fw-bold small mb-1 text-dark"><i class="fas fa-file-alt text-primary me-2"></i>Unggah Hasil Tes Lengkap (Detail)</label>
                            <input type="file" name="file_detail" class="form-control" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">*Format: PDF/DOC/DOCX</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm fw-bold px-3 btn-round" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 btn-round shadow-sm">Simpan & Selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        // Logic Modal Input Berkas
        function openModal(id, nama) {
            document.getElementById('modalNama').innerText = nama;

            // Set Action URL dinamis Laravel dengan penggantian placeholder ID
            let url = "{{ route('hasil.update', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('formHasil').action = url;

            // Trigger Modal Tampil ala Bootstrap 5
            var modalInput = new bootstrap.Modal(document.getElementById('modalHasil'));
            modalInput.show();
        }

        // Logic Modal Preview File Iframe
        function previewFile(url) {
            const frame = document.getElementById('previewFrame');
            frame.src = url;

            // Trigger Modal Preview Tampil ala Bootstrap 5
            var modalPreview = new bootstrap.Modal(document.getElementById('modalPreview'));
            modalPreview.show();
        }

        function closePreview() {
            // Sembunyikan modal secara paksa dan bersihkan src iframe agar load ulang aman
            var modalEl = document.getElementById('modalPreview');
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
            document.getElementById('previewFrame').src = "";
        }
    </script>
</body>
</html>
