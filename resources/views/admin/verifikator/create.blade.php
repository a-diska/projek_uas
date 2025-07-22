@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Tambah Verifikator</h4>

<div class="card">
    <div class="card-body">
        <form id="formVerifikator">
            @csrf

            <div class="mb-3">
                <label for="id_user" class="form-label">Nama User</label>
                <select class="form-select" id="id_user" name="id_user" required>
                    <option value="">-- Pilih Verifikator --</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tahapan" class="form-label">Tahapan</label>
                <input type="number" class="form-control" id="tahapan" name="tahapan" min="1" placeholder="Contoh: 1" required>
            </div>

            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="aktif">aktif</option>
                    <option value="nonaktif">nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.verifikator.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = localStorage.getItem('auth_token');

    function loadUsers() {
        fetch('/api/user?role=verifikator', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            const select = document.getElementById('id_user');
            select.innerHTML = '<option value="">-- Pilih Verifikator --</option>';

            if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                response.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.id} - ${user.nama}`;
                    select.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "-- Tidak ada user verifikator tersedia --";
                option.disabled = true;
                option.selected = true;
                select.appendChild(option);
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Gagal memuat data user",
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    function submitForm() {
        const form = document.getElementById('formVerifikator');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('/api/verifikator', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async res => {
                const response = await res.json();
                if (res.ok && response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: "Verifikator berhasil ditambahkan",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("admin.verifikator.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: response.message || "Gagal menyimpan data",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Terjadi kesalahan saat menyimpan data",
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });
    }

    loadUsers();
    submitForm();
});
</script>

@endsection
