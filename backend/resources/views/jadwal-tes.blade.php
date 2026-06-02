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
        /* Custom Utility Classes untuk Tampilan Modal yang Lebih Elegan */
        .bg-info-light {
            background-color: rgba(72, 171, 247, 0.1) !important;
        }
        .bg-primary-light {
            background-color: rgba(29, 124, 244, 0.1) !important;
        }
        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid #ebedf2 !important;
            }
            .border-start-md {
                border-left: 1px solid #ebedf2 !important;
            }
        }
        .avatar-lg {
            width: 48px !important;
            height: 48px !important;
        }
        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
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
                                                        <option value="Kantor Cabang">Kantor Cabang</option>
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
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="fw-bold text-dark fs-6">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</div>
                                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($item->lokasi == 'Home Visit')
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-home me-1"></i> Home Visit
                                                            </span>
                                                        @else
                                                            <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-building me-1"></i> Kantor Cabang
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if(!empty($item->nama_klien) && $item->nama_klien != '')
                                                            @php
                                                                $statusRaw = strtolower($item->status);
                                                                $badgeClass = 'badge-warning'; 
                                                                if(in_array($statusRaw, ['selesai', 'disetujui', 'diterima', 'sukses'])) {
                                                                    $badgeClass = 'badge-success';
                                                                } elseif(in_array($statusRaw, ['batal', 'ditolak'])) {
                                                                    $badgeClass = 'badge-danger';
                                                                }
                                                            @endphp
                                                            
                                                            <span class="badge {{ $badgeClass }} px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                @if($statusRaw == 'menunggu' || $statusRaw == 'konfirmasi')
                                                                    Menunggu
                                                                @else
                                                                    {{ ucfirst($item->status) }}
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="badge badge-primary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                Tersedia
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if(!empty($item->nama_klien) && $item->nama_klien != '')
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-label-info btn-round btn-lihat-klien fw-normal"
                                                                    data-id="{{ $item->id_jadwal }}"
                                                                    data-waktu="{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB"
                                                                    data-lokasi="{{ $item->lokasi }}">
                                                                <i class="fas fa-user me-1"></i> Lihat Detail Klien
                                                            </button>
                                                        @else
                                                            <span class="text-muted italic fs-8">Belum ada pemesan</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" id="form-hapus-{{ $item->id_jadwal }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-icon btn-link btn-danger btn-sm btn-hapus-jadwal" data-id="{{ $item->id_jadwal }}" data-bs-toggle="tooltip" title="Hapus Slot">
                                                                <i class="fas fa-trash-alt fs-6"></i>
                                                            </button>
                                                        </form>
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

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright text-center w-100">
                        2026, made with <i class="fa fa-heart heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="modal fade" id="modalKlien" tabindex="-1" aria-labelledby="modalKlienLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content card-round border-0 shadow-lg">
                <div class="modal-header bg-info text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalKlienLabel">
                        <i class="fas fa-user me-2"></i> Detail Informasi Klien
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light-50">
                    
                    <div class="row g-3 mb-4 p-3 bg-white rounded card-round shadow-sm border mx-0">
                        <div class="col-sm-6 border-end-md">
                            <div class="d-flex align-items-center">
                                <div class="p-2 bg-info-light text-info rounded-circle me-3 text-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-alt fs-5 mt-1"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase fw-bold text-muted fs-8 d-block">Waktu Pelaksanaan</small>
                                    <span class="fw-bold text-dark fs-6" id="modal-waktu-jadwal">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center ps-0 ps-md-3">
                                <div class="p-2 bg-primary-light text-primary rounded-circle me-3 text-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt fs-5 mt-1"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase fw-bold text-muted fs-8 d-block">Lokasi Tes</small>
                                    <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-semibold" id="modal-lokasi-jadwal">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="container-list-klien">
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-round w-100 fw-bold text-dark" data-bs-dismiss="modal">Tutup</button>

                    <form id="formUpdateStatus" method="POST" style="display:none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" id="inputStatus">
                        <button type="submit" id="btnSubmitStatus" class="btn btn-round fw-bold"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('[data-bs-toggle="tooltip"]').tooltip();

            // INISIALISASI DATATABLES PADA TABEL JADWAL
            $('#tabel-jadwal').DataTable({
                "pageLength": 10, 
                "order": [], 
                "language": {
                    "search": "Cari Slot:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data slot jadwal yang cocok",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Data tidak tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                }
            });

            // Logika Konfirmasi Hapus Slot Jadwal Menggunakan SweetAlert
            $('#tabel-jadwal').on('click', '.btn-hapus-jadwal', function(e) {
                e.preventDefault();
                var idJadwal = $(this).data('id');
                
                swal({
                    title: 'Apakah Anda yakin?',
                    text: "Slot jadwal ini akan dihapus secara permanen!",
                    type: 'warning',
                    buttons: {
                        cancel: {
                            visible: true,
                            text: 'Batal',
                            className: 'btn btn-secondary btn-round fw-bold'
                        },
                        confirm: {
                            text: 'Ya, Hapus!',
                            className: 'btn btn-danger btn-round fw-bold'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#form-hapus-' + idJadwal).submit();
                    }
                });
            });

            // Logika Konfirmasi Klik Aksi di Dalam Modal AJAX (Tolak / Buka Kembali Slot)
            $('#container-list-klien').on('click', '.btn-aksi-status', function(e) {
                e.preventDefault();
                var idJadwal = $(this).data('id');
                var statusTarget = $(this).data('status');
                
                var swalTitle = 'Konfirmasi Tindakan';
                var swalText = 'Apakah Anda yakin ingin memproses data ini?';
                var swalConfirmClass = 'btn btn-primary btn-round fw-bold';
                var swalConfirmText = 'Ya, Proses!';

                if (statusTarget === 'Ditolak') {
                    swalTitle = 'Tolak Permohonan?';
                    swalText = 'Permohonan booking slot dari klien ini akan ditolak.';
                    swalConfirmClass = 'btn btn-danger btn-round fw-bold';
                    swalConfirmText = 'Ya, Tolak!';
                } else if (statusTarget === 'Tersedia') {
                    swalTitle = 'Buka Kembali Slot?';
                    swalText = 'Slot ini akan dikosongkan dan dapat dipesan kembali oleh klien lain.';
                    swalConfirmClass = 'btn btn-success btn-round fw-bold';
                    swalConfirmText = 'Ya, Buka!';
                }

                swal({
                    title: swalTitle,
                    text: swalText,
                    type: 'warning',
                    buttons: {
                        cancel: {
                            visible: true,
                            text: 'Batal',
                            className: 'btn btn-secondary btn-round fw-bold'
                        },
                        confirm: {
                            text: swalConfirmText,
                            className: swalConfirmClass
                        }
                    }
                }).then((willConfirm) => {
                    if (willConfirm) {
                        executeUpdateStatus(idJadwal, statusTarget);
                    }
                });
            });

            // Logika Modal Detail Klien Ajax dengan Layout Baru yang Profesional
            $('.btn-lihat-klien').on('click', function() {
                var idJadwal = $(this).data('id');
                var infoWaktu = $(this).data('waktu');
                var infoLokasi = $(this).data('lokasi');
                
                $('#modal-waktu-jadwal').text(infoWaktu);
                $('#modal-lokasi-jadwal').html('<i class="fas fa-map-marker-alt me-1"></i> ' + infoLokasi);
                
                $('#container-list-klien').html(`
                    <div class="text-center py-4 text-muted bg-white border card-round shadow-sm">
                        <i class="fas fa-spinner fa-spin me-2 fs-5"></i>Memuat data pendaftar...
                    </div>
                `);
                $('#modalKlien').modal('show');

                $.ajax({
                    url: '/jadwal-tes/' + idJadwal + '/klien',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#container-list-klien').empty();
                        
                        if(data.length === 0) {
                            $('#container-list-klien').html(`
                                <div class="text-center py-5 bg-white rounded card-round border shadow-sm">
                                    <p class="text-muted mb-0 fw-medium italic">Belum ada klien yang memesan slot ini.</p>
                                </div>
                            `);
                            return;
                        }

                        $.each(data, function(index, item) {
                            var statusLower = item.status_jadwal.toLowerCase();
                            var badgeClass = 'badge-warning';
                            var displayStatus = item.status_jadwal;
                            
                            if (['selesai', 'disetujui', 'diterima', 'sukses'].includes(statusLower)) {
                                badgeClass = 'badge-success';
                            } else if (['batal', 'ditolak'].includes(statusLower)) {
                                badgeClass = 'badge-danger';
                            }

                            if (statusLower === 'menunggu' || statusLower === 'konfirmasi') {
                                displayStatus = 'Menunggu Konfirmasi';
                            } else {
                                displayStatus = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1).toLowerCase();
                            }

                            var cleanPhone = item.no_hp ? item.no_hp.replace(/[^0-9]/g, '') : '';
                            var waButton = item.no_hp ? `
                                <a href="https://wa.me/${cleanPhone}" target="_blank" class="btn btn-sm btn-success btn-round px-3">
                                    <i class="fab fa-whatsapp me-2"></i>Hubungi Klien
                                </a>
                            ` : '<span class="text-muted fs-8">-</span>';

                            var aksiHtml = '';
                            if (statusLower === 'menunggu') {
                                aksiHtml = `
                                    <button data-id="${item.id_jadwal}" data-status="Ditolak" class="btn btn-sm btn-outline-danger btn-round px-3 btn-aksi-status">
                                        <i class="fas fa-times-circle me-1"></i> Tolak Permohonan
                                    </button>
                                `;
                            } else if (statusLower === 'ditolak') {
                                aksiHtml = `
                                    <button data-id="${item.id_jadwal}" data-status="Tersedia" class="btn btn-sm btn-outline-success btn-round px-3 btn-aksi-status">
                                        <i class="fas fa-sync-alt me-1"></i> Buka Kembali Slot
                                    </button>
                                `;
                            } else {
                                aksiHtml = `<span class="badge bg-light text-muted border px-3 py-1 btn-round fs-8 fw-normal"><i class="fas fa-lock me-1"></i> Terkunci</span>`;
                            }

                            // Render Card Grid Profile Klien
                            var clientCard = `
                                <div class="card card-round border shadow-sm mb-0">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-5 mb-3 mb-md-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-lg me-3">
                                                        <span class="avatar-title rounded-circle bg-info-light text-info fw-bold fs-5">
                                                            ${item.nama_klien.charAt(0).toUpperCase()}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h5 class="fw-bold text-dark mb-1 fs-6">${item.nama_klien}</h5>
                                                        <span class="badge ${badgeClass} px-2.5 py-1 btn-round fs-8 fw-semibold">${displayStatus}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3 mb-md-0 border-start-md px-md-4">
                                                <div class="mb-2">
                                                    <small class="text-muted d-block fs-8 fw-bold text-uppercase">Nomor Handphone</small>
                                                    <span class="text-dark fw-medium fs-7">${item.no_hp ? item.no_hp : '-'}</span>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block fs-8 fw-bold text-uppercase">Catatan Tambahan</small>
                                                    <p class="text-muted mb-0 fs-7 italic" style="line-height: 1.4;">
                                                        "${item.komentar ? item.komentar : 'Tidak ada catatan khusus.'}"
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 text-md-end border-start-md">
                                                <div class="d-grid gap-2 d-md-block text-nowrap">
                                                    <div class="mb-2">${waButton}</div>
                                                    <div>${aksiHtml}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#container-list-klien').append(clientCard);
                        });
                    },
                    error: function() {
                        $('#container-list-klien').html(`
                            <div class="text-center py-4 text-danger bg-white border card-round shadow-sm">
                                <i class="fas fa-exclamation-triangle me-2"></i>Gagal mengambil data pendaftar klien.
                            </div>
                        `);
                    }
                });
            });
        });

        // Pemisahan Fungsi AJAX Update Status Utama
        function executeUpdateStatus(id, status) {
            $.ajax({
                url: '/jadwal-tes/' + id + '/update-status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    status: status
                },
                success: function(response) {
                    swal({
                        title: 'Berhasil!',
                        text: 'Status jadwal telah diperbarui.',
                        type: 'success',
                        buttons: {
                            confirm: {
                                className: 'btn btn-success btn-round'
                            }
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    swal({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat memperbarui status.',
                        type: 'error',
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger btn-round'
                            }
                        }
                    });
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
</body>
</html>