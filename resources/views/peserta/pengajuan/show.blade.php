@extends('peserta.index')

@section('content')
<h4 class="fw-bold py-3 mb-4 text-center">Detail Pengajuan</h4>

<div class="row justify-content-center" id="pengajuan-detail">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <!-- Judul Pengajuan -->
                <div class="mb-4">
                    <h3 class="text-primary fw-semibold mb-1" id="nama_workshop">Memuat...</h3>
                </div>

                <!-- Nama Peserta -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Nama</h6>
                    <p class="mb-0 text-dark" id="nama_user">Memuat...</p>
                </div>

                <!-- Pelayanan -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Pelayanan</h6>
                    <p class="mb-0 text-dark" id="nama_pelayanan">Memuat...</p>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Status</h6>
                    <p class="mb-0 text-dark" id="status_pengajuan">Memuat...</p>
                </div>

                <!-- Dokumen -->
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Dokumen</h6>
                    <ul id="list-dokumen" class="list-unstyled mb-0 text-dark small">
                        <li class="text-muted">Memuat...</li>
                    </ul>
                </div>

                <!-- Tombol Kembali -->
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('peserta.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const id = {!! json_encode($id_pengajuan) !!};
        loadPengajuanDetail(id);
    });

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    function loadPengajuanDetail(id) {
        const token = getToken();

        if (!token) {
            Swal.fire('Error', 'Token tidak tersedia. Silakan login ulang.', 'error');
            return;
        }

        fetch(`/api/pengajuan/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const p = response.data;

                document.getElementById('nama_workshop').textContent = p.workshop?.nama_workshop ?? '-';
                document.getElementById('nama_user').textContent = p.user?.nama ?? '-';
                document.getElementById('nama_pelayanan').textContent = p.pelayanan?.nama_pelayanan ?? '-';

                const statusEl = document.getElementById('status_pengajuan');
                switch (p.status) {
                    case 'diproses':
                        statusEl.innerHTML = '<span class="text-warning fw-semibold">diproses</span>';
                        break;
                    case 'disetujui':
                        statusEl.innerHTML = '<span class="text-success fw-semibold">disetujui</span>';
                        break;
                    case 'ditolak':
                        statusEl.innerHTML = '<span class="text-danger fw-semibold">ditolak</span>';
                        break;
                    default:
                        statusEl.textContent = '-';
                }

                const wrapper = document.getElementById('list-dokumen');
                wrapper.innerHTML = '';
                if (!p.dokumen || p.dokumen.length === 0) {
                    wrapper.innerHTML = '<li class="text-muted">Tidak ada dokumen.</li>';
                } else {
                    p.dokumen.forEach(doc => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = `/${doc.path}`;
                        a.textContent = doc.nama_file;
                        a.target = '_blank';
                        li.appendChild(a);
                        wrapper.appendChild(li);
                    });
                }

            } else {
                Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Gagal memuat data pengajuan.', 'error');
        });
    }
</script>
@endsection
