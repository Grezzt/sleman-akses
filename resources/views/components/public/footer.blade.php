<footer class="bg-sa-card border-t-2 border-sa-border mt-auto pt-12 pb-6">
    <div class="container px-4 mx-auto max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('Logo Image_margin.svg') }}" alt="Sleman Akses" class="h-10">
                    <h2 class="text-xl font-black text-sa-primary leading-none">Sleman Akses</h2>
                </div>
                <p class="text-sa-muted font-medium mb-4 max-w-md">
                    Platform pemetaan partisipatif untuk mewujudkan Kabupaten Sleman yang inklusif dan aksesibel bagi seluruh masyarakat, khususnya penyandang disabilitas fisik.
                </p>
            </div>

            <div>
                <h3 class="font-bold text-sa-text text-lg mb-4">Navigasi</h3>
                <ul class="space-y-2 font-medium">
                    <li><a href="{{ url('/') }}" class="text-sa-muted hover:text-sa-primary transition">Beranda</a></li>
                    <li><a href="{{ url('/map') }}" class="text-sa-muted hover:text-sa-primary transition">Peta Aksesibilitas</a></li>
                    <li><a href="{{ url('/about') }}" class="text-sa-muted hover:text-sa-primary transition">Tentang Kami</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-sa-text text-lg mb-4">Kontak</h3>
                <ul class="space-y-2 font-medium text-sa-muted">
                    <li><i class="fas fa-map-marker-alt w-5 text-sa-primary"></i> Kab. Sleman, DI Yogyakarta</li>
                    <li><i class="fas fa-envelope w-5 text-sa-primary"></i> info@slemanakses.id</li>
                </ul>
                <div class="flex gap-4 mt-4">
                    <a href="#" class="w-10 h-10 rounded-full card-solid flex items-center justify-center text-sa-primary hover:bg-sa-primary hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full card-solid flex items-center justify-center text-sa-primary hover:bg-sa-primary hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full card-solid flex items-center justify-center text-sa-primary hover:bg-sa-primary hover:text-white transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <div class="border-t border-sa-border pt-6 text-center text-sa-muted font-bold text-sm">
            <p>&copy; {{ date('Y') }} Sleman Akses. All rights reserved.</p>
        </div>
    </div>
</footer>
