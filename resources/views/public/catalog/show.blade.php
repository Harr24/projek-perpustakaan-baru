<h3>Daftar Salinan Buku yang Tersedia</h3>
<table border="1">
    <thead>
        <tr>
            <th>Kode Buku</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($book->copies as $copy)
            <tr>
                <td>{{ $copy->book_code }}</td>
                <td>{{ ucfirst($copy->status) }}</td>
                <td>
                    @if($copy->status == 'tersedia')
                        <form action="{{ route('borrow.store', $copy->id) }}" method="POST">
                            @csrf
                            <button type="submit">Pinjam Buku Ini</button>
                        </form>
                    @else
                        <span>-</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Saat ini tidak ada salinan buku yang tersedia.</td>
            </tr>
        @endforelse
    </tbody>
</table>