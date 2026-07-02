<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Aturan Chatbot (Rule-Based)</h3>
            <p class="text-muted mb-0">Kelola kata kunci dan jawaban otomatis untuk pelanggan.</p>
        </div>
        <button type="button" class="btn btn-primary fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAturan">
            <i class="bi bi-plus-lg me-1"></i> Tambah Aturan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            
            <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                <div>
                    <strong>Tips Pembuatan Aturan (Fuzzy Matching Aktif):</strong><br>
                    <small>Anda dapat memasukkan beberapa kata kunci alias/sinonim yang dipisahkan dengan tanda koma (Contoh: <i>"jam buka, jadwal, hari buka"</i>). Sistem menggunakan algoritma <i>Fuzzy Matching</i> sehingga tetap dapat mengenali kata kunci meskipun ada salah ketik (typo) atau variasi penulisan dari pelanggan.</small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center rounded-start">No</th>
                            <th width="25%">Kata Kunci (Keyword)</th>
                            <th>Jawaban (Response)</th>
                            <th width="15%" class="text-center rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aturan as $index => $a)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td><span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2 rounded-pill"><i class="bi bi-key me-1"></i> {{ $a->kata_kunci }}</span></td>
                            <td class="text-muted">{{ Str::limit($a->jawaban, 80) }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $a->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form action="{{ route('aturan-chatbot.destroy', $a->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Yakin ingin menghapus aturan chatbot ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $a->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Aturan Chatbot</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('aturan-chatbot.update', $a->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Kata Kunci (Bisa lebih dari satu, dipisahkan koma)</label>
                                                <input type="text" name="kata_kunci" class="form-control form-control-lg" value="{{ $a->kata_kunci }}" placeholder="Contoh: laptop, leptop, notebook" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Jawaban Sistem</label>
                                                <textarea name="jawaban" class="form-control" rows="4" required>{{ $a->jawaban }}</textarea>
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
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-robot fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada aturan chatbot.<br>Silakan tambah aturan baru agar AI bisa menjawab.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahAturan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Aturan Chatbot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('aturan-chatbot.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Kata Kunci (Bisa beberapa kata kunci dipisah koma)</label>
                            <input type="text" name="kata_kunci" class="form-control form-control-lg" placeholder="Contoh: jam buka, jadwal, operasional" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Jawaban Sistem</label>
                            <textarea name="jawaban" class="form-control" rows="4" placeholder="Contoh: Untuk keperluan gaming, kami merekomendasikan Laptop seri Asus ROG yang tersedia di katalog kami." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Aturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>