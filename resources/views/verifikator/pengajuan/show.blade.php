@extends('verifikator.index')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Verifikasi Pengajuan</h4>

    <div class="card shadow">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-person-circle me-1"></i> Nama Pengguna</label>
                <div class="form-control bg-light" id="nama_user">-</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-briefcase me-1"></i> Jenis Pelayanan</label>
                <div class="form-control bg-light" id="nama_pelayanan">-</div>
            </div>

            <div class="mb-3" id="workshop_section" style="display: none;">
                <label class="form-label fw-semibold"><i class="bi bi-gear me-1"></i> Workshop</label>
                <div class="form-control bg-light" id="nama_workshop">-</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i> Keterangan</label>
                <div class="form-control bg-light" id="keterangan">-</div>
            </div>

            <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-paperclip me-1"></i> Dokumen Terkait
                    </label>
                    <div id="dokumen_list" class="form-control bg-light" style="min-height: 40px;">
                        <em>Memuat...</em>
                    </div>
                </div>

            <div class="mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-pencil-square me-1"></i> Catatan Verifikator</label>
                <textarea class="form-control" id="catatan" rows="3" placeholder="Tulis catatan jika perlu..."></textarea>
            </div>

            <div class="text-end">
                <button class="btn btn-success me-2" onclick="kirimApproval('disetujui')">
                    <i class="bi bi-check-circle me-1"></i> Setujui
                </button>
                <button class="btn btn-danger me-2" onclick="kirimApproval('ditolak')">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
                <a href="{{ route('verifikator.pengajuan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
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
                headers: {
                    'Authorization': token
                }
            });

            const result = await res.json();

            if (!result.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || 'Gagal memuat data pengajuan.',
                    timer: 2000,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.href = "{{ route('verifikator.pengajuan.index') }}";
                    }
                });
                return;
            }

            const data = result.data;
            document.getElementById('nama_user').textContent = data.user?.nama ?? '-';
            document.getElementById('nama_pelayanan').textContent = data.pelayanan?.nama_pelayanan ?? '-';
            document.getElementById('keterangan').textContent = data.keterangan || '-';

            if (data.workshop) {
                document.getElementById('workshop_section').style.display = 'block';
                document.getElementById('nama_workshop').textContent = data.workshop?.nama_workshop ?? '-';
            }

            const dokumenBox = document.getElementById('dokumen_list');
dokumenBox.innerHTML = '';
if (data.dokumen && data.dokumen.length) {
    data.dokumen.forEach(d => {
        const link = document.createElement('a');

        // Gunakan URL Laravel storage:link
        link.href = `/storage/${d.path_file}`; // path_file = "dokumen/namafile.pdf"
        link.textContent = d.nama_file;
        link.classList.add('d-block');
        link.target = '_blank';
        link.rel = 'noopener noreferrer';

        dokumenBox.appendChild(link);
    });
} else {
    dokumenBox.innerHTML = '<em>Tidak ada dokumen</em>';
}


        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data detail.',
                timer: 2000,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = "{{ route('verifikator.pengajuan.index') }}";
                }
            });
        }
    }

    async function kirimApproval(status) {
        const catatan = document.getElementById('catatan').value;

        const konfirmasi = await Swal.fire({
            title: 'Konfirmasi',
            text: `Yakin ingin ${status === 'disetujui' ? 'menyetujui' : 'menolak'} pengajuan ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan',
            cancelButtonText: 'Batal'
        });

        if (!konfirmasi.isConfirmed) return;

        try {
            const res = await fetch(`/api/approval/${idPengajuan}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': token
                },
                body: JSON.stringify({
                    status,
                    catatan
                })
            });

            const result = await res.json();

            if (res.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.href = "{{ route('verifikator.pengajuan.index') }}";
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || 'Pengajuan sudah DITOLAK. Tidak dapat diubah.',
                    timer: 2000,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.href = "{{ route('verifikator.pengajuan.index') }}";
                    }
                });
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengirim keputusan.',
                timer: 2000,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = "{{ route('verifikator.pengajuan.index') }}";
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', loadDetail);
</script>
@endsection
