@extends('peserta.index')

@section('content')
    <h4 class="fw-bold py-3 mb-4 text-center">Buat Pengajuan Baru</h4>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form id="form-pengajuan" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="id_workshop" class="form-label fw-semibold">Workshop</label>
                            <select class="form-select" id="id_workshop" name="id_workshop">
                                <option value="" disabled selected>Memuat...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_pelayanan" class="form-label fw-semibold">Pelayanan</label>
                            <select class="form-select" id="id_pelayanan" name="id_pelayanan">
                                <option value="" disabled selected>Memuat...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="dokumen" class="form-label fw-semibold">Unggah Dokumen</label>
                            <input type="file" class="form-control" id="dokumen" name="dokumen[]" multiple
                                accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                            <label class="form-label mt-3">Preview Dokumen</label>
                            <div class="form-control bg-light" style="height:auto;">
                                <ul id="previewDokumen" class="list-unstyled mb-0" style="overflow-x: auto;"></ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('peserta.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-send"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadWorkshop();
            loadPelayanan();
        });

        function getToken() {
            return localStorage.getItem('auth_token');
        }

        document.getElementById('form-pengajuan').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            Swal.fire({
                title: 'Kirim Pengajuan?',
                text: 'Pastikan data sudah benar sebelum dikirim.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/api/pengajuan/peserta', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${getToken()}`,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(async res => {
                            const response = await res.json();
                            if (res.ok && response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Pengajuan dan dokumen berhasil dikirim.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('peserta.pengajuan.index') }}";
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Pengajuan gagal dikirim.',
                                });
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Server',
                                text: 'Terjadi kesalahan saat mengirim pengajuan.',
                            });
                        });
                }
            });
        });

        document.getElementById('dokumen').addEventListener('change', function(e) {
            const previewList = document.getElementById('previewDokumen');
            previewList.innerHTML = '';

            Array.from(e.target.files).forEach((file, index) => {
                const li = document.createElement('li');
                const blobUrl = URL.createObjectURL(file);
                const ext = file.name.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    li.innerHTML = `
                        <a href="${blobUrl}" target="_blank" class="text-decoration-none text-primary">
                            <i class="bi bi-file-image me-1"></i> ${file.name}
                        </a>`;
                } else if (['pdf'].includes(ext)) {
                    li.innerHTML = `
                        <a href="${blobUrl}" target="_blank" class="text-decoration-none text-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> ${file.name}
                        </a>`;
                } else {
                    li.innerHTML = `
                        <a href="${blobUrl}" target="_blank" class="text-decoration-none text-secondary">
                            <i class="bi bi-file-earmark-text me-1"></i> ${file.name}
                        </a>`;
                }

                previewList.appendChild(li);
            });
        });

        function loadWorkshop() {
            const select = document.getElementById('id_workshop');
            select.innerHTML = `<option disabled selected>Memuat workshop...</option>`;

            fetch('/api/workshop', {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        select.innerHTML = `<option value="">-- Pilih Workshop --</option>`;
                        response.data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_workshop;
                            option.textContent = item.nama_workshop;
                            select.appendChild(option);
                        });
                    } else {
                        select.innerHTML = `<option disabled selected>Gagal memuat data</option>`;
                    }
                })
                .catch(() => {
                    select.innerHTML = `<option disabled selected>Gagal memuat data</option>`;
                });
        }

        function loadPelayanan() {
            const select = document.getElementById('id_pelayanan');
            select.innerHTML = `<option disabled selected>Memuat pelayanan...</option>`;

            fetch('/api/pelayanan', {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        select.innerHTML = `<option value="">-- Pilih Pelayanan --</option>`;
                        response.data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_pelayanan;
                            option.textContent = item.nama_pelayanan;
                            select.appendChild(option);
                        });
                    } else {
                        select.innerHTML = `<option disabled selected>Gagal memuat data</option>`;
                    }
                })
                .catch(() => {
                    select.innerHTML = `<option disabled selected>Gagal memuat data</option>`;
                });
        }
    </script>
@endsection
