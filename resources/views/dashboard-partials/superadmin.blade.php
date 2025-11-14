{{-- ========================================================== --}}
{{-- BAGIAN 2: MANAJEMEN PENGGUNA --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.superadmin.petugas.index') }}">
<div class="nav-item-main"><span>Kelola Akun Petugas</span></div>
<span class="meta">Manajemen Staf</span>
</a>
<a class="nav-item" href="{{ route('admin.superadmin.members.index') }}">
<div class="nav-item-main"><span>Kelola Anggota</span></div>
<span class="meta">Siswa & Guru</span>
</a>

{{-- ========================================================== --}}
{{-- --- 🔥 INI DIA TAMBAHAN BARUNYA 🔥 --- --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.superadmin.majors.index') }}">
<div class="nav-item-main"><span>Kelola Jurusan</span></div>
<span class="meta">Manajemen Jurusan</span>
</a>
{{-- ========================================================== --}}


{{-- ========================================================== --}}
{{-- BAGIAN 3: MANAJEMEN SISTEM & KONTEN --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.superadmin.sliders.index') }}">
<div class="nav-item-main"><span>Kelola Hero Slider</span></div>
<span class="meta">Tampilan Depan</span>
</a>

{{-- ========================================================== --}}
{{-- PENAMBAHAN: Link ke Manajemen Tanggal Merah --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.superadmin.holidays.index') }}">
<div class="nav-item-main"><span>Manajemen Tanggal Merah</span></div>
<span class="meta">Atur denda & hari libur</span>
</a>
{{-- ========================================================== --}}


{{-- ========================================================== --}}
{{-- --- TAMBAHAN BARU: Link ke Jadwal Piket --- --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.superadmin.schedules.index') }}">
<div class="nav-item-main"><span>Kelola Jadwal Piket</span></div>
<span class="meta">Tampilan Depan</span>
</a>
{{-- ========================================================== --}}


<a class="nav-item" href="{{ route('admin.superadmin.fines.history') }}">
<div class="nav-item-main"><span>Kelola Riwayat Denda</span></div>
<span class="meta">Keuangan/Laporan</span>
</a>

{{-- ========================================================== --}}
{{-- BAGIAN 4: AKUN PRIBADI --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('profile.edit') }}">
<div class="nav-item-main"><span>Edit Profil Saya</span></div>
<span class="meta">Akun</span>
</a>