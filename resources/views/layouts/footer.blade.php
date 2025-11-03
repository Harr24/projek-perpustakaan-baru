{{-- File: resources/views/layouts/footer.blade.php --}}

<footer class="footer-section">
    <div class="container-fluid px-3 px-md-5 py-5">
        <div class="row g-4 mb-4">
            {{-- Column 1: Logo & Info - Lebih lebar --}}
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="footer-logo-circle">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <div class="footer-title">
                        <h5 class="mb-0">Perpustakaan SMK Multicomp Depok</h5>
                    </div>
                </div>
                <p class="footer-description mb-3">
                    Sistem Manajemen Perpustakaan Modern untuk mendukung kegiatan literasi dan pembelajaran di SMK Multicomp Depok.
                </p>
                <a href="https://www.google.com/maps/search/?api=1&query=SMK+Multicomp+Depok" target="_blank" class="footer-address">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Jl. Raya Kalimulya No.7, Kalimulya, Kec. Cilodong, Kota Depok, Jawa Barat 16413</span>
                </a>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h6 class="footer-heading mb-3">Informasi</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-chevron-right"></i> Layanan</a></li>
                    <li><a href="{{ route('catalog.librarians') }}"><i class="bi bi-chevron-right"></i> Pustakawan</a></li>
                    <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right"></i> Area Anggota</a></li>
                    <li><a href="{{ route('register') }}"><i class="bi bi-chevron-right"></i> Mendaftar Anggota</a></li>
                </ul>
            </div>
            {{-- Column 3: Tautan Penting --}}
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h6 class="footer-heading mb-3">Tautan Penting</h6>
                <ul class="footer-links">
                    <li>
                        <a href="https://www.instagram.com/smkmulticompofficial/" target="_blank">
                            <i class="bi bi-instagram"></i> Instagram Sekolah
                        </a>
                    </li>
                    <li>
                        <a href="https://smkmulticomp.sch.id/" target="_blank">
                            <i class="bi bi-globe"></i> Website Utama
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 4: GitHub Info --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading mb-3">Informasi Pengembang</h6>
                <p class="footer-subtitle mb-3">
                    Website ini dikembangkan untuk memudahkan manajemen perpustakaan sekolah.
                </p>
                <a href="https://github.com/Harr24/projek-perpustakaan-baru" target="_blank" class="footer-btn-github">
                    <i class="bi bi-github"></i> Lihat di GitHub
                </a>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="row">
            <div class="col-12">
                <div class="footer-copyright text-center pt-4">
                    © {{ date('Y') }} — SMK Multicomp Depok
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Styles */
.footer-section {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: white;
    margin-top: auto;
}

.footer-logo-circle {
    width: 56px;
    height: 56px;
    background-color: rgba(255,255,255,0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.footer-title h5 {
    color: white;
    font-weight: 600;
    font-size: 1.125rem;
    line-height: 1.3;
}

.footer-description {
    color: rgba(255,255,255,0.85);
    font-size: 0.9375rem;
    line-height: 1.6;
}

.footer-subtitle {
    color: rgba(255,255,255,0.75);
    font-size: 0.875rem;
    line-height: 1.5;
}

.footer-address {
    color: rgba(255,255,255,0.85);
    font-size: 0.9375rem;
    display: flex;
    align-items: flex-start;
    gap: 0.625rem;
    padding: 0.75rem;
    background-color: rgba(255,255,255,0.05);
    border-radius: 8px;
    text-decoration: none; 
    transition: all 0.3s ease; 
}

.footer-address:hover { 
    background-color: rgba(255,255,255,0.1);
    color: white;
}

.footer-address i {
    margin-top: 0.15rem;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.footer-heading {
    color: white;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1rem !important;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.875rem;
}

.footer-links a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.footer-links a:hover {
    color: white;
    padding-left: 8px;
}

.footer-links a i {
    font-size: 1rem; 
}

.footer-search-wrapper {
    width: 100%;
}

.footer-search-input {
    background-color: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    padding: 0.75rem 1.125rem;
    font-size: 0.9375rem;
    border-radius: 8px;
}

.footer-search-input::placeholder {
    color: rgba(255,255,255,0.5);
}

.footer-search-input:focus {
    background-color: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.35);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.1);
}

.btn-search {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-search:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    color: white;
}

.footer-btn-github {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    background-color: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.9375rem;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 100%;
}

.footer-btn-github:hover {
    background-color: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.35);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255,255,255,0.1);
}

.footer-btn-github i {
    font-size: 1.25rem;
}

.footer-copyright {
    color: rgba(255,255,255,0.7);
    font-size: 0.9375rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 1.5rem;
}

/* Responsive */
@media (max-width: 991px) {
    .footer-section .col-lg-3,
    .footer-section .col-lg-4,
    .footer-section .col-lg-2 {
        text-align: left;
    }
}

@media (max-width: 767px) {
    .footer-logo-circle {
        margin: 0;
    }
    
    .footer-address {
        text-align: left;
    }
    
    .footer-links a {
        justify-content: flex-start;
    }
}
</style>