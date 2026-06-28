<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Teknisi</h3>
            <p class="text-muted mb-0">Kelola akun dan daftar teknisi perbaikan barang di toko.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTeknisi">
            <i class="bi bi-plus-lg me-1"></i> Tambah Teknisi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Oops! Ada beberapa kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center rounded-start">No</th>
                            <th>Nama Teknisi</th>
                            <th>No. WhatsApp</th>
                            <th>Email</th>
                            <th width="20%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teknisi as $index => $t)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $t->name }}</td>
                            <td class="fw-semibold text-primary">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $t->no_whatsapp) }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $t->no_whatsapp }}
                                </a>
                            </td>
                            <td>{{ $t->email }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $t->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('data-teknisi.destroy', $t->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus teknisi {{ $t->name }}?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Teknisi -->
                        <div class="modal fade" id="modalEdit{{ $t->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Teknisi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('data-teknisi.update', $t->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control form-control-lg" value="{{ $t->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Nomor WhatsApp</label>
                                                <input type="text" name="no_whatsapp" class="form-control form-control-lg" value="{{ $t->no_whatsapp }}" required placeholder="Contoh: 08123456789">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Email Address</label>
                                                <input type="email" name="email" class="form-control form-control-lg" value="{{ $t->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Password (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control form-control-lg" placeholder="Minimal 8 karakter">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-person-badge fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data teknisi.<br>Silakan tambah teknisi baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Teknisi -->
    <div class="modal fade" id="modalTambahTeknisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Teknisi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('data-teknisi.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="Nama lengkap teknisi" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Nomor WhatsApp</label>
                            <input type="text" name="no_whatsapp" class="form-control form-control-lg" placeholder="Contoh: 08123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="email@domain.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Teknisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
