<?php
$homePath = 'c:/laragon/www/rancaupas-ecounique/resources/views/home.blade.php';
$content = file_get_contents($homePath);

$replacements = [
    '> Destinasi Alam Terbaik' => '> {{ __(\'messages.hero_badge\') }}',
    'Selamat Datang <br class="hidden sm:block">' => '{{ __(\'messages.hero_welcome\') }} <br class="hidden sm:block">',
    'di Ranca
                        Upas' => '{{ __(\'messages.hero_in\') }}',
    'Ranca Upas adalah tempat wisata alam yang menawarkan pengalaman unik dengan udara segar dan pemandangan indah, serta mengajak anda mencoba berbagai jenis camping seru yang tersedia disini. Temukan paket wisata terbaik kami dan buat liburan anda tak terlupakan.' => '{{ __(\'messages.hero_desc\') }}',
    '> Lihat
                        Informasi' => '> {{ __(\'messages.btn_view_info\') }}',
    '> Cari
                            Rekomendasi' => '> {{ __(\'messages.btn_find_recs\') }}',
    '> Login & Cari
                            Rekomendasi' => '> {{ __(\'messages.btn_login_recs\') }}',
    'Spot Menarik' => '{{ __(\'messages.feat_1_title\') }}',
    'Pesona bentang alam yang tak terlupakan' => '{{ __(\'messages.feat_1_desc\') }}',
    'Fasilitas Lengkap' => '{{ __(\'messages.feat_2_title\') }}',
    'Beragam utilitas pendukung kenyamanan' => '{{ __(\'messages.feat_2_desc\') }}',
    'Akses Terjangkau' => '{{ __(\'messages.feat_3_title\') }}',
    'Rute dan jarak tempuh yang mudah' => '{{ __(\'messages.feat_3_desc\') }}',
    'Menjelajahi Ranca Upas' => '{{ __(\'messages.info_badge\') }}',
    'Penawaran Spesial' => '{{ __(\'messages.info_title\') }}',
    'Berbagai jenis petualangan eksotis yang terpadu
                        lembut dengan alam menanti Anda.' => '{{ __(\'messages.info_desc\') }}',
    'ulasan)' => '{{ __(\'messages.reviews\') }})',
    'Lihat Detail' => '{{ __(\'messages.btn_view_detail\') }}',
    'Tiket
                                            WNI' => '{{ __(\'messages.ticket_wni\') }}',
    'Tiket
                                            WNA' => '{{ __(\'messages.ticket_wna\') }}',
    'ulasan pengunjung' => '{{ __(\'messages.visitor_reviews\') }}',
    'Login untuk Review' => '{{ __(\'messages.btn_login_review\') }}',
    'Deskripsi' => '{{ __(\'messages.desc\') }}',
    'Belum ada deskripsi.' => '{{ __(\'messages.no_desc\') }}',
    'Fasilitas' => '{{ __(\'messages.facilities\') }}',
    'Lokasi' => '{{ __(\'messages.location\') }}',
    'Buka di Google Maps' => '{{ __(\'messages.open_map\') }}',
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}
file_put_contents($homePath, $content);

$visitorPath = 'c:/laragon/www/rancaupas-ecounique/resources/views/visitor.blade.php';
$content = file_get_contents($visitorPath);

$replacements = [
    'Dashboard Pengunjung' => '{{ __(\'messages.nav_visitor_dashboard\') }}',
    'Beranda' => '{{ __(\'messages.nav_home\') }}',
    'Keluar' => '{{ __(\'messages.nav_logout\') }}',
    'Selamat Datang,' => '{{ __(\'messages.welcome_name\') }}',
    ', {{ auth()->user()->name }}! 👋' => '!!', // Fix welcome later in blade
    'Jelajahi dan Temukan paket wisata terbaik kami dan buat liburan anda tak terlupakan.' => '{{ __(\'messages.welcome_desc\') }}',
    'Katalog Wisata' => '{{ __(\'messages.tab_catalog\') }}',
    'Rekomendasi' => '{{ __(\'messages.tab_recs\') }}',
    'Profil' => '{{ __(\'messages.tab_profile\') }}',
    'Cari wisata berdasarkan nama...' => '{{ __(\'messages.search_ph\') }}',
    'Urutkan:' => '{{ __(\'messages.sort_by\') }}',
    '⭐ Rating Tertinggi' => '{{ __(\'messages.sort_rating\') }}',
    '🔥 Terpopuler' => '{{ __(\'messages.sort_pop\') }}',
    '💰 Harga Termurah' => '{{ __(\'messages.sort_price_low\') }}',
    '💎 Harga Termahal' => '{{ __(\'messages.sort_price_high\') }}',
    '🔤 Nama A-Z' => '{{ __(\'messages.sort_az\') }}',
    'Menampilkan' => '{{ __(\'messages.showing\') }}',
    'destinasi wisata' => '{{ __(\'messages.destinations\') }}',
    'ulasan<' => '{{ __(\'messages.reviews\') }}<',
    'Belum ada deskripsi.' => '{{ __(\'messages.no_desc\') }}',
    'Lihat Detail' => '{{ __(\'messages.btn_view_detail\') }}',
    'Tidak Ditemukan' => '{{ __(\'messages.not_found_title\') }}',
    'Tidak ada wisata yang cocok dengan pencarian Anda. Coba kata kunci lain.' => '{{ __(\'messages.not_found_desc\') }}',
    'Form Rekomendasi' => '{{ __(\'messages.rec_form_title\') }}',
    'Isi preferensi Anda untuk mendapatkan rekomendasi wisata di Ranca Upas.' => '{{ __(\'messages.rec_form_desc\') }}',
    '1. Jenis Wisata' => '{{ __(\'messages.type_label\') }}',
    'Semua Jenis' => '{{ __(\'messages.all_types\') }}',
    '2. Budget / Biaya per orang' => '{{ __(\'messages.budget_label\') }}',
    'Semua Harga' => '{{ __(\'messages.all_prices\') }}',
    '3. Fasilitas yang diinginkan' => '{{ __(\'messages.fac_label\') }}',
    '(Pilih lebih dari satu)' => '{{ __(\'messages.choose_more\') }}',
    '4. Jarak dari lokasi Wisata Rancaupas' => '{{ __(\'messages.dist_label\') }}',
    'Semua Jarak' => '{{ __(\'messages.all_dist\') }}',
    'Dekat (< 5 km)' => '{{ __(\'messages.dist_close\') }}',
    'Menengah (5 - 15 km)' => '{{ __(\'messages.dist_med\') }}',
    'Jauh (> 15 km)' => '{{ __(\'messages.dist_far\') }}',
    '5. Rating Minimum' => '{{ __(\'messages.rating_label\') }}',
    '(opsional)' => '{{ __(\'messages.optional\') }}',
    'ke atas' => '{{ __(\'messages.and_up\') }}',
    'Reset rating' => '{{ __(\'messages.reset_rating\') }}',
    'Cari Rekomendasi' => '{{ __(\'messages.btn_search_rec\') }}',
    'Sistem menggunakan filter terstruktur untuk mencari dan mengurutkan rekomendasi wisata sesuai preferensi Anda.' => '{{ __(\'messages.sys_info\') }}',
    'Belum Ada Rekomendasi' => '{{ __(\'messages.no_rec_title\') }}',
    'Isi form preferensi di atas dan klik tombol "Cari Rekomendasi" untuk menampilkan destinasi terbaik Anda.' => '{{ __(\'messages.no_rec_desc\') }}',
    'Hasil Rekomendasi' => '{{ __(\'messages.rec_res_title\') }}',
    'Hasil perhitungan berdasarkan preferensi Anda' => '{{ __(\'messages.rec_res_desc\') }}',
    'Biaya Tiket' => '{{ __(\'messages.ticket_cost\') }}',
    'Ditemukan ' => '{{ __(\'messages.found\') }} ',
    'tempat wisata yang sesuai dengan filter dan preferensi Anda.' => '{{ __(\'messages.match_desc\') }}',
    'Tidak ada tempat wisata yang sesuai dengan filter Anda. Silakan longgarkan kriteria pencarian.' => '{{ __(\'messages.no_match_desc\') }}',
    'Edit Profil' => '{{ __(\'messages.edit_prof\') }}',
    'Nama Lengkap' => '{{ __(\'messages.full_name\') }}',
    'Asal Pengunjung' => '{{ __(\'messages.visitor_origin\') }}',
    'Domestik (WNI)' => '{{ __(\'messages.domestic\') }}',
    'Mancanegara (WNA)' => '{{ __(\'messages.intl\') }}',
    'Ubah Password' => '{{ __(\'messages.change_pass\') }}',
    'Kosongkan jika tidak ingin mengubah password' => '{{ __(\'messages.change_pass_desc\') }}',
    'Password Baru' => '{{ __(\'messages.new_pass\') }}',
    'Minimal 6 karakter' => '{{ __(\'messages.min_char\') }}',
    'Konfirmasi Password Baru' => '{{ __(\'messages.conf_new_pass\') }}',
    'Simpan Perubahan' => '{{ __(\'messages.save_changes\') }}'
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}
// Fix welcome string specifically
$content = preg_replace('/\{\{ __\(\'messages\.welcome_name\'\)\}\} \!\!/s', '{{ __(\'messages.welcome_name\', [\'name\' => auth()->user()->name]) }}', $content);

file_put_contents($visitorPath, $content);
echo "Done";
