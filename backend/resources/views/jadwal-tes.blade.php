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
                                <i class="fas fa-calendar-check me-1"></i> {{ $jadwal->total() }} Slot Diterbitkan
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
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0 align-middle">
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
                                                        <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-icon btn-link btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Slot">
                                                                <i class="fas fa-trash-alt fs-6"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-5 text-center text-muted italic">Belum ada slot jadwal yang diterbitkan.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($jadwal->hasPages())
                                        <div class="card-footer d-flex justify-content-between align-items-center bg-white border-top py-3 px-4">
                                            <div class="text-muted fs-8">
                                                Menampilkan {{ $jadwal->firstItem() }} sampai {{ $jadwal->lastItem() }} dari {{ $jadwal->total() }} data
                                            </div>
                                            <div>
                                                {{ $jadwal->links('pagination::bootstrap-5') }}
                                            </div>
                                        </div>
                                    @endif

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
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted text-uppercase d-block fw-bold fs-8">Waktu Pelaksanaan</small>
                            <span class="fw-bold text-dark fs-6" id="modal-waktu-jadwal">-</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted text-uppercase d-block fw-bold fs-8">Lokasi</small>
                            <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-normal" id="modal-lokasi-jadwal">-</span>
                        </div>
                    </div>
                    
                    <div class="table-responsive border rounded card-round">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase" style="width: 5%">No</th>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase">Nama Klien</th>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase">No. HP / WhatsApp</th>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase">Status</th>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase">Catatan</th>
                                    <th class="px-3 py-2 text-muted fs-8 text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="container-list-klien">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-round w-100 fw-bold" data-bs-dismiss="modal">Tutup</button>

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
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('.btn-lihat-klien').on('click', function() {
                var idJadwal = $(this).data('id');
                var infoWaktu = $(this).data('waktu');
                var infoLokasi = $(this).data('lokasi');
                
                $('#modal-waktu-jadwal').text(infoWaktu);
                $('#modal-lokasi-jadwal').html('<i class="fas fa-map-marker-alt me-1"></i> ' + infoLokasi);
                
                $('#container-list-klien').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data pendaftar...</td></tr>');
                $('#modalKlien').modal('show');

                $.ajax({
                    url: '/jadwal-tes/' + idJadwal + '/klien',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#container-list-klien').empty();
                        
                        if(data.length === 0) {
                            $('#container-list-klien').html('<tr><td colspan="5" class="text-center py-4 text-muted italic">Belum ada klien yang memesan slot ini.</td></tr>');
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
                                displayStatus = 'Menunggu';
                            } else {
                                displayStatus = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1).toLowerCase();
                            }

                            var cleanPhone = item.no_hp ? item.no_hp.replace(/[^0-9]/g, '') : '';
                            var waLink = item.no_hp ? `<a href="https://wa.me/${cleanPhone}" target="_blank" class="text-success fw-semibold"><i class="fab fa-whatsapp me-1"></i> ${item.no_hp}</a>` : '-';
                            var aksiHtml = '';
if (statusLower === 'menunggu') {
    // Menggunakan item.id_jadwal yang sekarang sudah dikirim oleh Controller
    aksiHtml = `<button onclick="updateStatus(${item.id_jadwal}, 'Ditolak')" class="btn btn-danger btn-sm btn-round">Tolak</button>`;
} else if (statusLower === 'ditolak') {
    // Menggunakan item.id_jadwal yang sekarang sudah dikirim oleh Controller
    aksiHtml = `<button onclick="updateStatus(${item.id_jadwal}, 'Tersedia')" class="btn btn-success btn-sm btn-round"><i class="fas fa-sync-alt me-1"></i> Buka Kembali</button>`;
} else {
    aksiHtml = `<span class="text-muted fs-8">Tidak ada aksi</span>`;
}
                            var row = `
                                <tr>
                                    <td class="px-3 py-2 fw-bold text-muted">${index + 1}</td>
                                    <td class="px-3 py-2 fw-bold text-dark">${item.nama_klien}</td>
                                    <td class="px-3 py-2">${waLink}</td>
                                    <td class="px-3 py-2"><span class="badge ${badgeClass} fw-normal text-capitalize">${displayStatus}</span></td>
                                    <td class="px-3 py-2 text-muted fs-8 italic">${item.komentar ? item.komentar : '-'}</td>
                                    <td class="px-3 py-2">${aksiHtml}</td>
                                </tr>
                            `;
                            $('#container-list-klien').append(row);
                        });
                    },
                    error: function() {
                        $('#container-list-klien').html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Gagal mengambil data klien.</td></tr>');
                    }
                });
            });
        });
        function updateStatus(id, status) {
    if(confirm('Yakin ingin mengubah status jadwal ini menjadi ' + status + '?')) {
        $.ajax({
            url: '/jadwal-tes/' + id + '/update-status',
            type: 'POST', // Atau PUT
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                status: status
            },
            success: function(response) {
                alert('Status berhasil diubah!');
                location.reload(); // Refresh halaman untuk melihat perubahan
            },
            error: function(xhr) {
                alert('Gagal mengubah status.');
                console.log(xhr.responseText);
            }
        });
    }
}
    </script>
</body>
</html>