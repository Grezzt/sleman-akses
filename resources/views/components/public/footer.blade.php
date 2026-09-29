<footer class="bg-primary mt-auto pt-10 pb-6 text-surface relative overflow-hidden border-t-2 border-border-solid" style="background-image: url('{{ asset('landing-hijau.svg') }}'); background-size: cover; background-position: center;">
    <div class="container px-4 mx-auto max-w-7xl relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-primary p-2 rounded-xl border-2 border-border-solid shadow-[2px_2px_0px_0px_#0F0F0F] inline-flex">
                        <img src="{{ asset('Logo Image_margin.svg') }}" alt="Sleman Akses" class="h-8">
                    </div>
                    <h2 class="text-2xl font-black text-secondary leading-none" style="color: var(--color-surface);">Sleman Akses</h2>
                </div>
                <p class="text-surface/90 text-base font-medium mb-6 max-w-md leading-relaxed">
                    Platform pemetaan digital partisipatif untuk mewujudkan Kabupaten Sleman yang inklusif dan aksesibel bagi seluruh masyarakat, khususnya penyandang disabilitas fisik.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 rounded-lg bg-surface border-2 border-text-onsurface flex items-center justify-center text-primary text-lg shadow-[2px_2px_0px_0px_#0F0F0F] hover:-translate-y-1 hover:shadow-[1px_1px_0px_0px_#0F0F0F] transition-all"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-surface border-2 border-text-onsurface flex items-center justify-center text-primary text-lg shadow-[2px_2px_0px_0px_#0F0F0F] hover:-translate-y-1 hover:shadow-[1px_1px_0px_0px_#0F0F0F] transition-all"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-surface border-2 border-text-onsurface flex items-center justify-center text-primary text-lg shadow-[2px_2px_0px_0px_#0F0F0F] hover:-translate-y-1 hover:shadow-[1px_1px_0px_0px_#0F0F0F] transition-all"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div>
                <h3 class="font-black text-secondary text-lg mb-4" style="color: var(--color-surface);">Navigasi</h3>
                <ul class="space-y-3 font-bold text-base">
                    <li><a href="{{ url('/') }}" class="text-surface hover:text-secondary hover:translate-x-1 inline-block transition-all">Beranda</a></li>
                    <li><a href="{{ url('/map') }}" class="text-surface hover:text-secondary hover:translate-x-1 inline-block transition-all">Peta Aksesibilitas</a></li>
                    <li><a href="{{ route('public.articles.index') }}" class="text-surface hover:text-secondary hover:translate-x-1 inline-block transition-all">Berita & Edukasi</a></li>
                    <li><a href="{{ url('/about') }}" class="text-surface hover:text-secondary hover:translate-x-1 inline-block transition-all">Tentang Kami</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-black text-secondary text-lg mb-4" style="color: var(--color-surface);">Hubungi Kami</h3>
                <ul class="space-y-3 font-bold text-surface text-base">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-secondary text-lg w-5 text-center"></i>
                        <span>Kab.Sleman,<br>DI Yogyakarta</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-secondary text-lg w-5 text-center"></i>
                        <a href="mailto:info@slemanakses.id" class="hover:text-secondary transition-colors">info@slemanakses.id</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t-2 border-surface/20 pt-6 flex flex-col md:flex-row justify-between items-center text-surface/80 font-bold text-sm gap-4">
            <p>&copy; {{ date('Y') }} Sleman Akses.</p>
            {{-- <div class="flex gap-4">
                <a href="#" class="hover:text-secondary transition-colors">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-secondary transition-colors">Kebijakan Privasi</a>
            </div> --}}
        </div>
    </div>
</footer>
