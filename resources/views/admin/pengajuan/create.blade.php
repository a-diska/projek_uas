@extends('admin.index')

@section('content')
    <h4 class="fw-bold py-3 mb-4">Tambah Pengajuan</h4>

    <div class="card">
        <div class="card-body">
            <form id="formPengajuan" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="id_user" class="form-label">Nama User</label>
                    <select class="form-select" id="id_user" name="id_user" required>
                        <option value="">-- Pilih Peserta --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_workshop" class="form-label">Nama Workshop</label>
                    <select class="form-select" id="id_workshop" name="id_workshop">
                        <option value="">-- Pilih Workshop --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_pelayanan" class="form-label">Nama Pelayanan</label>
                    <select class="form-select" id="id_pelayanan" name="id_pelayanan" required>
                        <option value="">-- Pilih Pelayanan --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dokumen" class="form-label">Unggah Dokumen</label>
                    <input type="file" class="form-control" id="dokumen" name="dokumen[]" multiple
                        accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">

                    <div class="mb-3">
                        <label class="form-label">Preview Dokumen</label>
                        <div class="border rounded p-2 bg-light">
                            <ul id="previewDokumen" class="list-unstyled mb-0"></ul>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('auth_token');

        fetch('/api/user?role=peserta', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('id_user');
                    data.data.forEach(user => {
                        select.innerHTML += `<option value="${user.id}">${user.id} - ${user.nama}</option>`;
                    });
                }
            });

        fetch('/api/workshop', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('id_workshop');
                    data.data.forEach(workshop => {
                        select.innerHTML +=
                            `<option value="${workshop.id_workshop}">${workshop.nama_workshop}</option>`;
                    });
                }
            });

        // Load pelayanan
        fetch('/api/pelayanan', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('id_pelayanan');
                    data.data.forEach(pelayanan => {
                        select.innerHTML +=
                            `<option value="${pelayanan.id_pelayanan}">${pelayanan.nama_pelayanan}</option>`;
                    });
                }
            });

        document.getElementById('dokumen').addEventListener('change', function(e) {
            const preview = document.getElementById('previewDokumen');
            preview.innerHTML = '';

            Array.from(e.target.files).forEach(file => {
                const url = URL.createObjectURL(file);
                const li = document.createElement('li');
                li.innerHTML = `<a href="${url}" target="_blank">${file.name}</a>`;
                preview.appendChild(li);
            });
        });

        document.getElementById('formPengajuan').addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const dokumenInput = document.getElementById('dokumen');

            try {
                const res = await fetch('/api/pengajuan', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await res.json();
                if (!result.success) {
                    throw new Error(result.message || 'Gagal menyimpan pengajuan.');
                }

                const id_pengajuan = result.data.id_pengajuan;

                // Upload dokumen jika ada
                if (dokumenInput.files.length > 0) {
                    const dokumenForm = new FormData();
                    dokumenForm.append('id_pengajuan', id_pengajuan);
                    for (const file of dokumenInput.files) {
                        dokumenForm.append('dokumen[]', file);
                    }

                    const resDoc = await fetch('/api/dokumen', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        },
                        body: dokumenForm
                    });

                    const resultDoc = await resDoc.json();
                    if (!resultDoc.success) {
                        throw new Error(resultDoc.message || 'Gagal upload dokumen.');
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Pengajuan dan dokumen berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('admin.pengajuan.index') }}";
                });


            } catch (err) {
                console.error(err);
                Swal.fire('Error', err.message || 'Terjadi kesalahan saat menyimpan.', 'error');
            }
        });
    </script>
@endsection
