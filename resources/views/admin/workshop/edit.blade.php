@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Edit Workshop</h4>

<div class="card p-4">
    <form id="formEditWorkshop">
        <div class="mb-3">
            <label for="id_workshop" class="form-label">ID Workshop</label>
            <input type="text" id="id_workshop" class="form-control" value="{{ $id_workshop }}" readonly>
        </div>

        <div class="mb-3">
            <label for="nama_workshop" class="form-label">Nama Workshop</label>
            <input type="text" id="nama_workshop" name="nama_workshop" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" id="lokasi" name="lokasi" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.workshop.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
    function getToken() {
        return localStorage.getItem('auth_token'); // ✅ Ganti ke auth_token
    }

    document.addEventListener('DOMContentLoaded', function () {
        const id = document.getElementById('id_workshop').value;
        const token = getToken();

        if (!token) {
            Swal.fire('Error', 'Token tidak ditemukan. Silakan login ulang.', 'error');
            return;
        }

        // Ambil data workshop
        fetch(`/api/workshop/${id}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                const data = response.data;
                document.getElementById('nama_workshop').value = data.nama_workshop;
                document.getElementById('tanggal_mulai').value = data.tanggal_mulai;
                document.getElementById('tanggal_selesai').value = data.tanggal_selesai;
                document.getElementById('lokasi').value = data.lokasi;
            } else {
                Swal.fire('Gagal', response.message || 'Data workshop tidak ditemukan.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Gagal mengambil data workshop.', 'error');
        });

        // Submit form update
        document.getElementById('formEditWorkshop').addEventListener('submit', function (e) {
            e.preventDefault();

            const nama = document.getElementById('nama_workshop').value.trim();
            const mulai = document.getElementById('tanggal_mulai').value;
            const selesai = document.getElementById('tanggal_selesai').value;
            const lokasi = document.getElementById('lokasi').value.trim();

            if (new Date(selesai) < new Date(mulai)) {
                Swal.fire('Peringatan', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.', 'warning');
                return;
            }

            const payload = {
                nama_workshop: nama,
                tanggal_mulai: mulai,
                tanggal_selesai: selesai,
                lokasi: lokasi
            };

            fetch(`/api/workshop/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Workshop berhasil diperbarui!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('admin.workshop.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Gagal memperbarui data.', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
            });
        });
    });
</script>
@endsection
