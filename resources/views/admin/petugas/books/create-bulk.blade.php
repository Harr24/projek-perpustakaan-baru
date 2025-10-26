@extends('layouts.app') {{-- Pastikan ini sesuai dengan layout utama Anda --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Tambah Banyak Buku</h1>
            <p class="text-muted mb-0 small">Masukkan informasi beberapa buku sekaligus.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Buku
            </a>
            {{-- Tombol simpan akan ada di bawah form --}}
        </div>
    </div>

    {{-- Notifikasi Error Validasi --}}
    {{-- (Kode notifikasi error tetap sama) --}}
     @if ($errors->has('general'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan!</h6>
             <p class="mb-0 small">{{ $errors->first('general') }}</p>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
     @endif
     @if ($errors->has('books.*'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Periksa Kembali Input Anda:</h6>
            <ul class="mb-0 small ps-4">
                 @foreach ($errors->get('books.*') as $fieldErrors)
                     @foreach ($fieldErrors as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 @endforeach
                 {{-- Menampilkan error duplikasi kode_awal (jika ada) --}}
                 @foreach ($errors->keys() as $key)
                     @if (preg_match('/^books\.\d+\.initial_code$/', $key) && !in_array($key, array_keys($errors->get('books.*'))))
                         <li>{{ $errors->first($key) }}</li>
                     @endif
                 @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
     @endif


    <form action="{{ route('admin.petugas.books.store.bulk.form') }}" method="POST" id="bulk-book-form">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 card-title fw-semibold">Daftar Buku Baru</h5>
                <button type="button" class="btn btn-success btn-sm" id="add-book-row">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Baris
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light-subtle small text-muted">
                            <tr>
                                {{-- Penyesuaian Lebar Kolom --}}
                                <th class="ps-3 py-2" style="width: 18%;">Judul <span class="text-danger">*</span></th>
                                <th class="py-2" style="width: 15%;">Penulis <span class="text-danger">*</span></th>
                                <th class="py-2" style="width: 13%;">Genre <span class="text-danger">*</span></th>
                                <th class="py-2" style="width: 10%;">Kode Awal <span class="text-danger">*</span></th>
                                <th class="py-2" style="width: 7%;">Stok <span class="text-danger">*</span></th>
                                <th class="py-2" style="width: 8%;">Thn Terbit</th>
                                <th class="py-2" style="width: 14%;">Sinopsis</th> {{-- Tambah Kolom Sinopsis --}}
                                <th class="py-2" style="width: 10%;">Buku Paket</th> {{-- Singkat Label --}}
                                <th class="pe-3 py-2 text-end" style="width: 5%;"></th> {{-- Kolom Kosong untuk Tombol Hapus (Header Aksi Dihapus) --}}
                            </tr>
                        </thead>
                        <tbody id="book-rows-container">
                            @php
                                $rowCount = max(1, count(old('books', [[]])));
                            @endphp

                            @for ($i = 0; $i < $rowCount; $i++)
                            <tr class="book-row border-top">
                                <td class="ps-3 py-2">
                                    <input type="text" name="books[{{ $i }}][title]" class="form-control form-control-sm @error('books.'.$i.'.title') is-invalid @enderror" placeholder="Judul Buku" value="{{ old('books.'.$i.'.title') }}" required>
                                </td>
                                <td class="py-2">
                                    <input type="text" name="books[{{ $i }}][author]" class="form-control form-control-sm @error('books.'.$i.'.author') is-invalid @enderror" placeholder="Nama Penulis" value="{{ old('books.'.$i.'.author') }}" required>
                                </td>
                                <td class="py-2">
                                    <select name="books[{{ $i }}][genre_id]" class="form-select form-select-sm @error('books.'.$i.'.genre_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option> {{-- Singkat --}}
                                        @foreach ($genres as $genre)
                                            <option value="{{ $genre->id }}" {{ old('books.'.$i.'.genre_id') == $genre->id ? 'selected' : '' }}>
                                                {{ $genre->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2">
                                    <input type="text" name="books[{{ $i }}][initial_code]" class="form-control form-control-sm @error('books.'.$i.'.initial_code') is-invalid @enderror" placeholder="Cont: LPL" maxlength="10" value="{{ old('books.'.$i.'.initial_code') }}" required>
                                </td>
                                <td class="py-2">
                                    <input type="number" name="books[{{ $i }}][stock]" class="form-control form-control-sm @error('books.'.$i.'.stock') is-invalid @enderror" placeholder="1-100" min="1" max="100" value="{{ old('books.'.$i.'.stock', 1) }}" required>
                                </td>
                                <td class="py-2">
                                    <input type="number" name="books[{{ $i }}][publication_year]" class="form-control form-control-sm @error('books.'.$i.'.publication_year') is-invalid @enderror" placeholder="Cont: {{ date('Y') }}" min="1900" max="{{ date('Y') }}" value="{{ old('books.'.$i.'.publication_year') }}">
                                </td>
                                {{-- ========================================================== --}}
                                {{-- PENAMBAHAN: Kolom Input Sinopsis --}}
                                {{-- ========================================================== --}}
                                <td class="py-2">
                                     <textarea name="books[{{ $i }}][synopsis]" rows="1" class="form-control form-control-sm @error('books.'.$i.'.synopsis') is-invalid @enderror" placeholder="Opsional">{{ old('books.'.$i.'.synopsis') }}</textarea>
                                </td>
                                {{-- ========================================================== --}}
                                <td class="py-2 text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" name="books[{{ $i }}][is_textbook]" value="1" id="is_textbook_{{ $i }}" {{ old('books.'.$i.'.is_textbook') ? 'checked' : '' }}>
                                        <label class="form-check-label ms-1 visually-hidden" for="is_textbook_{{ $i }}">Ya</label>
                                    </div>
                                </td>
                                <td class="pe-3 py-2 text-end">
                                    @if($i > 0 || $rowCount > 1)
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-book-row" title="Hapus Baris Ini">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-danger btn-sm invisible" disabled>
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end py-3">
                <button type="submit" class="btn btn-danger fw-medium">
                    <i class="bi bi-save-fill me-1"></i> Simpan Semua Buku
                </button>
            </div>
        </div>
    </form>

</div>

@endsection

@push('scripts')
{{-- Script JavaScript untuk menambah/hapus baris --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('book-rows-container');
        const addButton = document.getElementById('add-book-row');
        let rowIndex = container.querySelectorAll('.book-row').length;

        function updateRemoveButtons() {
            const rows = container.querySelectorAll('.book-row');
            rows.forEach((row, index) => {
                let removeButton = row.querySelector('.remove-book-row');
                 // Selalu kolom terakhir sekarang
                const actionCell = row.cells[row.cells.length - 1];

                if (rows.length <= 1) {
                    if (!removeButton) {
                        actionCell.innerHTML = `<button type="button" class="btn btn-outline-danger btn-sm invisible" disabled><i class="bi bi-trash-fill"></i></button>`;
                    } else {
                        removeButton.classList.add('invisible');
                        removeButton.disabled = true;
                    }
                } else {
                    if (!removeButton || removeButton.classList.contains('invisible')) {
                         actionCell.innerHTML = `<button type="button" class="btn btn-outline-danger btn-sm remove-book-row" title="Hapus Baris Ini"><i class="bi bi-trash-fill"></i></button>`;
                    } else {
                        removeButton.disabled = false;
                    }
                }
            });
        }


        addButton.addEventListener('click', function () {
            const lastRow = container.querySelector('.book-row:last-child'); // Ambil baris terakhir
            if (!lastRow) return;

            const newRow = lastRow.cloneNode(true); // Duplikasi

            // Reset nilai input/textarea di baris baru dan update index name/id
            newRow.querySelectorAll('input, select, textarea').forEach(input => { // Tambahkan textarea
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
                }
                const id = input.getAttribute('id');
                 if (id) {
                    const newId = id.replace(/_\d+$/, `_${rowIndex}`);
                    input.setAttribute('id', newId);
                    const label = newRow.querySelector(`label[for="${id}"]`);
                    if (label) {
                       label.setAttribute('for', newId);
                    }
                }

                // Reset value
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else if (input.tagName === 'SELECT') {
                     input.selectedIndex = 0;
                } else if (input.name && input.name.includes('[stock]')) {
                     input.value = '1';
                }
                 else { // Termasuk textarea sinopsis
                    input.value = '';
                }
                input.classList.remove('is-invalid');
            });

            // Pastikan tombol hapus ada
            const actionCell = newRow.cells[newRow.cells.length - 1];
            actionCell.innerHTML = `<button type="button" class="btn btn-outline-danger btn-sm remove-book-row" title="Hapus Baris Ini"><i class="bi bi-trash-fill"></i></button>`;

            container.appendChild(newRow);
            rowIndex++;
            updateRemoveButtons();
        });

        container.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.remove-book-row');
            if (removeButton) {
                const rowToRemove = removeButton.closest('.book-row');
                if (container.querySelectorAll('.book-row').length > 1) {
                    rowToRemove.remove();
                    // Tidak perlu update rowIndex saat menghapus
                    updateRemoveButtons();
                 }
            }
        });

        updateRemoveButtons(); // Panggil saat load
    });
</script>
@endpush

