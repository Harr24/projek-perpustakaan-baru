@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: #d9534f;">Riwayat Pembayaran Denda</h1>
            <p class="text-muted mb-0 small">Daftar transaksi pembayaran denda (termasuk cicilan).</p>
        </div>
         <div class="d-flex gap-2">
             <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                 <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
             </a>
             <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-outline-danger btn-sm">
                 <i class="bi bi-cash-coin me-1"></i> Bayar Denda
             </a>
         </div>
    </div>

    {{-- Form Filter --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-3">
            {{-- Action diarahkan ke rute Petugas --}}
            <form action="{{ route('admin.petugas.fines.history') }}" method="GET" class="row gx-2 gy-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="search" class="form-label small">Cari Nama/Judul</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Nama siswa atau judul buku...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="year" class="form-label small">Tahun Bayar</label>
                    <select name="year" id="year" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->year }}" {{ request('year') == $year->year ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="month" class="form-label small">Bulan Bayar</label>
                    <select name="month" id="month" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        @php
                            $months = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger btn-sm flex-grow-1"><i class="bi bi-funnel-fill"></i> Filter</button>
                        
                        {{-- Tombol Export --}}
                        <a href="{{ route('admin.petugas.fines.export', request()->query()) }}" class="btn btn-success btn-sm" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel-fill"></i> <span class="d-none d-lg-inline">Export</span>
                        </a>
                        
                        @if(request()->has('search') || request()->has('year') || request()->has('month'))
                            <a href="{{ route('admin.petugas.fines.history') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat Pembayaran --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white py-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2"></i>Log Transaksi Masuk</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0 small">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="py-2 px-3">Tgl Bayar</th>
                            <th class="py-2 px-3">Nama Siswa</th>
                            <th class="py-2 px-3">Kelas / Mapel</th>
                            <th class="py-2 px-3">Judul Buku</th>
                            <th class="py-2 px-3 text-end">Nominal Bayar</th> 
                            <th class="py-2 px-3 text-center">Petugas</th>
                            <th class="py-2 px-3 text-center">Status Peminjaman</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-3">
                                    {{ $payment->created_at->format('d/m/Y') }}
                                    <span class="text-muted small d-block">{{ $payment->created_at->format('H:i') }}</span>
                                </td>

                                <td class="px-3 fw-medium">
                                    {{ $payment->borrowing->user->name ?? 'User Dihapus' }}
                                </td>
                                
                                {{-- ========================================================== --}}
                                {{-- 🔥 INI PERBAIKANNYA: Menggunakan class_info 🔥 --}}
                                {{-- ========================================================== --}}
                                <td class="px-3">
                                    {{ $payment->borrowing->user->class_info ?? '-' }}
                                </td>
                                {{-- ========================================================== --}}

                                <td class="px-3">
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                        {{ $payment->borrowing->bookCopy->book->title ?? 'Buku Dihapus' }}
                                    </span>
                                    <span class="d-block text-muted" style="font-size: 0.8em;">
                                        {{ $payment->borrowing->bookCopy->book_code ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-3 text-end fw-bold text-success">
                                    + Rp{{ number_format($payment->amount_paid, 0, ',', '.') }}
                                </td>

                                <td class="px-3 text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ $payment->processedBy->name ?? 'System' }}
                                    </span>
                                </td>
                                
                                <td class="px-3 text-center">
                                    @if($payment->borrowing->fine_status == 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Lunas</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-wallet2 d-block fs-1 mb-2 opacity-50"></i>
                                    Belum ada data pembayaran denda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                    @if($payments->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="px-3 py-2 text-end">Total Uang Masuk (Halaman ini):</td>
                            <td class="px-3 py-2 text-end text-success">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2" colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            @if ($payments->hasPages())
                 <div class="card-footer bg-white border-top-0 py-2">
                     {{ $payments->links() }}
                 </div>
            @endif
        </div>
    </div>
</div>
@endsection