@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Edit Verifikator</h4>

<div class="card p-4">
    <form id="formEditVerifikator">
        @csrf
        <input type="hidden" id="id_verifikator" name="id_verifikator" value="{{ $id_verifikator }}">

        <div class="mb-3">
            <label for="id_user" class="form-label">Nama User</label>
            <select id="id_user" name="id_user" class="form-select" required>
                <option value="">-- Pilih User --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tahapan" class="form-label">Tahapan</label>
            <input type="number" id="tahapan" name="tahapan" class="form-control" min="1" placeholder="Contoh: 1" required>
        </div>

        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" id="jabatan" name="jabatan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.verifikator.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const id = document.getElementById('id_verifikator').value;
    const token = localStorage.getItem('auth_token');

    if (!token) {
        Swal.fire('Akses Ditolak', 'Silakan login ulang', 'error');
        return;
    }

    fetch(`/api/verifikator/${id}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            const data = response.data;
            document.getElementById('tahapan').value = data.tahapan || '';
            document.getElementById('jabatan').value = data.jabatan || '';
            document.getElementById('status').value = data.status || '';
            loadUsers(data.id_user);
        } else {
            Swal.fire('Gagal', response.message || 'Data tidak ditemukan.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Gagal memuat data verifikator.', 'error');
    });

    function loadUsers(selectedId = null) {
        fetch('/api/user?role=verifikator', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            const select = document.getElementById('id_user');
            select.innerHTML = '<option value="">-- Pilih User --</option>';

            if (response.success && Array.isArray(response.data)) {
                response.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.id} - ${user.nama}`;
                    if (String(user.id) === String(selectedId)) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.text = "-- Tidak ada user verifikator tersedia --";
                option.disabled = true;
                option.selected = true;
                select.appendChild(option);
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Gagal memuat data user.', 'error');
        });
    }

    const tahapanInput = document.getElementById('tahapan');
    tahapanInput.addEventListener('input', () => {
        if (tahapanInput.value < 1) {
            tahapanInput.value = 1;
        }
    });

    document.getElementById('formEditVerifikator').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        fetch(`/api/verifikator/${id}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil',
                    text: 'Data verifikator berhasil diperbarui.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => {
                    window.location.href = "{{ route('admin.verifikator.index') }}";
                }, 1500);
            } else {
                Swal.fire('Gagal', response.message || 'Gagal menyimpan data.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
        });
    });
});
</script>
@endsection
