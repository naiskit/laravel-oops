<?php

/*
 * Judul error, sama untuk semua tone agar pengunjung selalu tahu persis
 * apa yang terjadi. Tone hanya mengganti teks di bawah judul
 * (lihat formal.php, casual.php, fun.php).
 */
return [
    'statuses' => [
        403 => 'Akses Ditolak',
        404 => 'Halaman Tidak Ditemukan',
        419 => 'Sesi Telah Berakhir',
        429 => 'Terlalu Banyak Permintaan',
        500 => 'Kesalahan Server',
        503 => 'Sedang Dalam Pemeliharaan',
    ],

    // Dipakai untuk kode status yang tidak ada di atas.
    'default' => 'Terjadi Kesalahan',

    'error_label' => 'Error',
];
