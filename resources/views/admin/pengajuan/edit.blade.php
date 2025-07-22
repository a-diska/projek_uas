@extends('admin.index')

@section('content')
    <h4 class="fw-bold py-3 mb-4">Edit Pengajuan</h4>

    <div class="card p-4">
        <form id="formEditPengajuan" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id_pengajuan" name="id_pengajuan" value="{{ $id_pengajuan }}">

            <div class="mb-3">
                <label class="form-label">Nama User</label>
                <input type="text" id="nama" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Workshop</label>
                <input type="text" id="workshop" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Pelayanan</label>
                <input type="text" id="pelayanan" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Dokumen Lama</label>
                <div class="border rounded p-2 bg-light">
                    <ul id="daftar-dokumen-lama" class="list-unstyled mb-0 small text-primary-emphasis"></ul>
                </div>
            </div>

            <div class="mb-3">
                <label for="dokumenBaru" class="form-label">Unggah Dokumen Baru (opsional)</label>
                <input type="file" name="dokumen[]" id="dokumenBaru" class="form-control" multiple
                    accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
            </div>

            <div class="mb-3">
                <label class="form-label">Preview Dokumen Baru</label>
                <div class="border rounded p-2 bg-light">
                    <ul id="previewDokumenBaru" class="list-unstyled mb-0 small text-secondary"></ul>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Perbarui</button>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const id = document.getElementById('id_pengajuan').value;
            const token = localStorage.getItem('auth_token');

            fetch(`/api/pengajuan/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        const data = response.data;
                        document.getElementById('nama').value = data.user?.nama || '-';
                        document.getElementById('workshop').value = data.workshop?.nama_workshop || '-';
                        document.getElementById('pelayanan').value = data.pelayanan?.nama_pelayanan || '-';

                        const wrapper = document.getElementById('daftar-dokumen-lama');
                        wrapper.innerHTML = '';

                        const dokumen = data.dokumen ?? [];

                        if (dokumen.length > 0) {
                            dokumen.forEach(doc => {
                                const url = `${window.location.origin}/${doc.path}`;
                                wrapper.innerHTML += `
                        <li class="mb-1">
                            <a href="${url}" target="_blank">${doc.nama_file}</a>
                        </li>`;
                            });
                        } else {
                            wrapper.innerHTML = '<li class="text-muted">Tidak ada dokumen.</li>';
                        }
                    } else {
                        Swal.fire('Gagal', response.message || 'Data pengajuan tidak ditemukan.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat data pengajuan.', 'error');
                });

            document.getElementById('dokumenBaru').addEventListener('change', function(e) {
                const wrapper = document.getElementById('previewDokumenBaru');
                wrapper.innerHTML = '';

                if (e.target.files.length > 0) {
                    Array.from(e.target.files).forEach(file => {
                        const blobURL = URL.createObjectURL(file);
                        wrapper.innerHTML += `
                    <li class="mb-1">
                        <a href="${blobURL}" target="_blank">${file.name}</a>
                    </li>`;
                    });
                } else {
                    wrapper.innerHTML = '<li class="text-muted">Tidak ada file dipilih.</li>';
                }
            });

            document.getElementById('formEditPengajuan').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData();
                formData.append('_method', 'PUT');

                const files = document.getElementById('dokumenBaru').files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('dokumen_baru[]', files[i]);
                }

                fetch(`/api/pengajuan/${id}`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message ||
                                    'Pengajuan dan dokumen berhasil diperbarui.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                window.location.href = "/admin/pengajuan";
                            }, 1600);
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal memperbarui pengajuan.',
                                'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
                    });
            });
        });
    </script>
@endsection
