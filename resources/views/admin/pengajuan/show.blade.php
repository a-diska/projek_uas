@extends('admin.index')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
   <h4 class="fw-bold py-3 mb-4">Detail Pengajuan</h4>

    <div class="card shadow">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">ID Pengajuan</label>
                <div class="form-control bg-light" id="id_pengajuan">Memuat...</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Pengguna</label>
                <div class="form-control bg-light" id="nama_user">Memuat...</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Workshop</label>
                <div class="form-control bg-light" id="nama_workshop">Memuat...</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Pelayanan</label>
                <div class="form-control bg-light" id="nama_pelayanan">Memuat...</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dokumen</label>
                <div class="form-control bg-light" id="dokumen_list">
                    <em>Memuat dokumen...</em>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>

<script>
    const idPengajuan = `{{ request()->segment(3) }}`;
    const token = localStorage.getItem('auth_token');

    async function loadDetail() {
        try {
            const res = await fetch(`/api/pengajuan/${idPengajuan}`, {
                headers: { 'Authorization': token }
            });
            const result = await res.json();

            if (!result.success) {
                Swal.fire('Gagal', result.message || 'Data tidak ditemukan.', 'error');
                return;
            }

            const data = result.data;
            document.getElementById('id_pengajuan').textContent = data.id_pengajuan ?? '-';
            document.getElementById('nama_user').textContent = data.user?.nama ?? '-';
            document.getElementById('nama_workshop').textContent = data.workshop?.nama_workshop ?? '-';
            document.getElementById('nama_pelayanan').textContent = data.pelayanan?.nama_pelayanan ?? '-';

            const dokumenBox = document.getElementById('dokumen_list');
            dokumenBox.innerHTML = '';

            if (data.dokumen && data.dokumen.length > 0) {
                const list = document.createElement('ul');
                list.classList.add('list-unstyled');

                data.dokumen.forEach(d => {
                    const item = document.createElement('li');
                    const link = document.createElement('a');

                    const url = d.path.startsWith('storage/') ? `/${d.path}` : `/storage/${d.path}`;
                    link.href = url;
                    link.textContent = d.nama_file ?? 'Lihat Dokumen';
                    link.classList.add('text-primary', 'text-decoration-underline');
                    link.target = '_blank';

                    item.appendChild(link);
                    list.appendChild(item);
                });

                dokumenBox.appendChild(list);
            } else {
                dokumenBox.innerHTML = '<em>Tidak ada dokumen tersedia.</em>';
            }

        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Gagal memuat data detail.', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', loadDetail);
</script>
@endsection
