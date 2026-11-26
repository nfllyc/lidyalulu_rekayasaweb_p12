
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare Store - Data Produk</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f5fa;
            color: #352b3b;
        }

        .navbar {
            background: #a77bc2;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .btn-purple {
            background: #a77bc2;
            color: white;
        }

        .btn-purple:hover {
            background: #9368ae;
            color: white;
        }

        .table thead {
            background: #ede3f5;
        }

        .page-title {
            color: #6f4a83;
            font-weight: 700;
        }
    </style>
    
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            🐾 PETCARE STORE
        </a>
        <span class="text-white">Admin Dashboard</span>
    </div>
</nav>

<!-- Main Content -->
<div class="container py-5">

    <div class="mb-4">
        <h2 class="page-title">Manajemen Produk</h2>
        <p class="text-secondary">
            Kelola data produk pakan dan perlengkapan hewan.
        </p>
    </div>

    <!-- Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card main-card p-3">
                <small class="text-secondary">Total Produk</small>
                <h3 id="totalProduk" class="fw-bold">0</h3>
            </div>
        </div>
    </div>

    <!-- Tabel Produk -->
    <div class="card main-card p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Produk</h5>

            <button class="btn btn-purple"
                    onclick="openAddModal()">
                + Tambah Produk
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Ukuran</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="productTable">
                    <tr>
                        <td colspan="7" class="text-center">
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah/Edit Produk -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    Tambah Produk
                </h5>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="productForm">

                    <input type="hidden" id="productId">

                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" id="name"
                               class="form-control" required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select id="category" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option>Makanan Kucing</option>
                            <option>Makanan Anjing</option>
                            <option>Vitamin</option>
                            <option>Perlengkapan Hewan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ukuran</label>
                        <input type="text" id="size"
                               class="form-control" maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea id="description"
                                  class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" id="price"
                               class="form-control" min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" id="stock"
                               class="form-control" min="0" required>
                    </div>

                    <button type="submit"
                            class="btn btn-purple w-100">
                        Simpan Produk
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- AJAX JavaScript -->
<script>
const API_URL = '/api/products';

const productModal = new bootstrap.Modal(
    document.getElementById('productModal')
);

// Mengamankan teks dari data API sebelum ditampilkan di HTML
function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

// READ: Mengambil data produk menggunakan AJAX GET
async function loadProducts() {
    const table = document.getElementById('productTable');

    try {
        const response = await fetch(API_URL, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error('Gagal mengambil data produk');
        }

        const products = result.data;

        document.getElementById('totalProduk').textContent =
            products.length;

        if (products.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Belum ada data produk.
                    </td>
                </tr>
            `;
            return;
        }

        table.innerHTML = products.map((product, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHTML(product.name)}</td>
                <td>${escapeHTML(product.category)}</td>
                <td>${escapeHTML(product.size || '-')}</td>
                <td>Rp ${Number(product.price).toLocaleString('id-ID')}</td>
                <td>${escapeHTML(product.stock)}</td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        onclick="editProduct(${Number(product.id)})">
                        Edit
                    </button>

                    <button class="btn btn-danger btn-sm"
                        onclick="deleteProduct(${Number(product.id)})">
                        Hapus
                    </button>
                </td>
            </tr>
        `).join('');

    } catch (error) {
        table.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-danger">
                    ${escapeHTML(error.message)}
                </td>
            </tr>
        `;
    }
}

// Membuka modal tambah produk
function openAddModal() {
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Produk';

    productModal.show();
}

// CREATE dan UPDATE menggunakan AJAX
document.getElementById('productForm')
    .addEventListener('submit', async function(event) {

    event.preventDefault();

    const id = document.getElementById('productId').value;

    const data = {
        name: document.getElementById('name').value.trim(),
        category: document.getElementById('category').value,
        size: document.getElementById('size').value.trim(),
        description: document.getElementById('description').value.trim(),
        price: document.getElementById('price').value,
        stock: document.getElementById('stock').value
    };

    if (!data.name || !data.category ||
        data.price === '' || data.stock === '' ||
        Number(data.price) < 0 ||
        !Number.isInteger(Number(data.stock)) ||
        Number(data.stock) < 0) {

        Swal.fire('Validasi Gagal',
            'Periksa kembali nama, kategori, harga, dan stok produk.',
            'warning');
        return;
    }

    const url = id ? `${API_URL}/${id}` : API_URL;
    const method = id ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 422) {
                const messages = Object.values(result.errors || {})
                    .flat()
                    .join('\n');

                throw new Error(messages || 'Validasi gagal');
            }

            throw new Error(result.message || 'Gagal menyimpan produk');
        }

        productModal.hide();
        document.getElementById('productForm').reset();

        await loadProducts();

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            timer: 1800,
            showConfirmButton: false
        });

    } catch (error) {
        Swal.fire('Gagal!', error.message, 'error');
    }
});

// UPDATE: Mengambil detail produk untuk diedit
async function editProduct(id) {
    try {
        const response = await fetch(`${API_URL}/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error('Data produk tidak ditemukan');
        }

        const product = result.data;

        document.getElementById('productId').value = product.id;
        document.getElementById('name').value = product.name;
        document.getElementById('category').value = product.category;
        document.getElementById('size').value = product.size || '';
        document.getElementById('description').value =
            product.description || '';
        document.getElementById('price').value = product.price;
        document.getElementById('stock').value = product.stock;

        document.getElementById('modalTitle').textContent =
            'Edit Produk';

        productModal.show();

    } catch (error) {
        Swal.fire('Gagal!', error.message, 'error');
    }
}

// DELETE: Menghapus produk menggunakan AJAX
async function deleteProduct(id) {
    const confirmation = await Swal.fire({
        title: 'Hapus Produk?',
        text: 'Data produk yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    });

    if (!confirmation.isConfirmed) return;

    try {
        const response = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menghapus produk');
        }

        await loadProducts();

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            timer: 1800,
            showConfirmButton: false
        });

    } catch (error) {
        Swal.fire('Gagal!', error.message, 'error');
    }
}

// Menampilkan data saat halaman pertama kali dibuka
document.addEventListener('DOMContentLoaded', loadProducts);
</script>

</body>
</html>
