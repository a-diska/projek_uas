@extends('admin.index')

@section('content')
<h4 class="fw-bold py-3 mb-4">Log Approval</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Log Approval</h5>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>ID Pengajuan</th>
                    <th>Verifikator</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody id="tabel-log-approval">
                <tr>
                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end m-3" id="pagination-wrapper"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadLogApproval();
    });

    function loadLogApproval(page = 1) {
        const token = localStorage.getItem('auth_token');
        const tbody = document.getElementById("tabel-log-approval");
        const pagination = document.getElementById("pagination-wrapper");

        if (!token) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Token tidak ditemukan. Harap login ulang.</td></tr>`;
            return;
        }

        fetch(`/api/logs/approval?page=${page}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(response => {
            const data = response.data;
            const currentPage = response.current_page;
            const lastPage = response.last_page;

            tbody.innerHTML = "";

            if (Array.isArray(data) && data.length > 0) {
                data.forEach((log, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${(currentPage - 1) * 20 + index + 1}</td>
                            <td class="text-center">${log.id_pengajuan ?? '-'}</td>
                            <td class="text-center">${log.verifikator?.nama ?? '-'}</td>
                            <td class="text-center">${log.status}
                            </td>

                            <td class="text-center">${log.catatan ?? '-'}</td>
                            <td class="text-center">${formatTanggal(log.created_at)}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Data log belum tersedia.</td></tr>`;
            }

            renderPagination(pagination, currentPage, lastPage);
        })
        .catch(err => {
            console.error("Gagal memuat data:", err);
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>`;
        });
    }

    function renderPagination(wrapper, current, total) {
        let html = '<ul class="pagination mb-0">';
        for (let i = 1; i <= total; i++) {
            html += `<li class="page-item ${i === current ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" onclick="loadLogApproval(${i})">${i}</a>
                    </li>`;
        }
        html += '</ul>';
        wrapper.innerHTML = html;
    }

    function formatTanggal(tanggal) {
        const d = new Date(tanggal);
        return d.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>
@endsection
