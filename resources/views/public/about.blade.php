@extends('layouts.public')

@section('title', 'Tentang Sleman Akses')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-background pt-32 lg:pt-40 border-b-2 border-border-solid">
    <div class="relative z-10 mx-auto px-5 lg:px-14 flex flex-col items-center w-full max-w-5xl text-center pb-24">
        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold text-primary bg-background border-2 border-border-solid mb-8 shadow-[2px_2px_0px_0px_#D4D4D4]">
            <i class="fas fa-info-circle mr-2"></i> Tentang Kami
        </div>
        <h1 class="font-bold leading-tight text-primary mb-6" style="font-size: clamp(36px, 5vw, 64px); letter-spacing: -0.02em;">
            Mewujudkan Sleman yang <br>
            <span class="text-text-onsurface">Inklusif & Aksesibel</span>
        </h1>
        <p class="text-text-muted" style="font-size: clamp(18px, 2vw, 22px); line-height: 1.6; max-width: 600px;">
            Sleman Akses adalah platform pemetaan digital partisipatif yang memungkinkan masyarakat untuk menemukan dan melaporkan ketersediaan fasilitas ramah disabilitas di Kabupaten Sleman.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-24 bg-surface">
    <div class="container px-4 mx-auto max-w-5xl">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-24">
            
            <!-- Latar Belakang -->
            <div class="md:col-span-5">
                <h2 class="font-black text-text-onsurface mb-6" style="font-size: clamp(24px, 3vw, 36px); line-height: 1.1; letter-spacing: -0.02em;">
                    Hak Dasar Setiap Individu
                </h2>
                <div class="prose prose-lg text-text-muted font-medium leading-relaxed">
                    <p class="mb-4">Mobilitas adalah hak dasar setiap individu. Namun, penyandang disabilitas fisik sering kali menghadapi hambatan dalam mengakses fasilitas umum karena keterbatasan informasi mengenai aksesibilitas suatu lokasi.</p>
                    <p>Melalui Sleman Akses, kami berupaya menjembatani kesenjangan informasi ini dengan teknologi partisipatif.</p>
                </div>
            </div>

            <!-- Core Values -->
            <div class="md:col-span-7">
                <div class="grid grid-cols-1 gap-0 border-y-2 border-l-2 border-border-solid bg-background shadow-solid">
                    
                    <div class="p-8 border-r-2 border-b-2 border-border-solid relative group hover:bg-secondary/10 transition-colors">
                        <div class="mb-4 text-primary">
                            <i class="fas fa-map-marked-alt text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-text-onsurface text-xl mb-2">Pemetaan Partisipatif</h3>
                        <p class="text-text-muted text-sm leading-relaxed">
                            Melibatkan masyarakat secara aktif (crowdsourcing) untuk memperbarui data aksesibilitas secara real-time. Siapapun bisa berkontribusi mengirim foto dan lokasi.
                        </p>
                    </div>
                    
                    <div class="p-8 border-r-2 border-border-solid relative group hover:bg-secondary/10 transition-colors">
                        <div class="mb-4 text-primary">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-text-onsurface text-xl mb-2">Data Tervalidasi</h3>
                        <p class="text-text-muted text-sm leading-relaxed">
                            Setiap laporan dari masyarakat tidak langsung dipublikasikan. Laporan akan divalidasi oleh admin untuk memastikan keakuratan informasi.
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <div class="text-center mt-24">
            <h3 class="text-2xl font-black text-text-onsurface mb-6">Mari Berkontribusi</h3>
            <a href="{{ url('/map') }}" class="btn-primary-solid bg-primary text-surface border-border-solid shadow-[4px_4px_0px_0px_#bfd852] hover:shadow-[2px_2px_0px_0px_#bfd852] px-8 py-3 text-lg">
                Mulai Menjelajah
            </a>
        </div>
    </div>
</section>
@endsection
