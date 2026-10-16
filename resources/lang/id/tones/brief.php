<?php

// Singkat dan langsung: satu kalimat, tanpa basa-basi.
// Cocok untuk aplikasi internal dan dashboard yang dipakai setiap hari.

return [
    'statuses' => [
        403 => [
            'message' => 'Akun ini tidak memiliki akses ke halaman ini.',
            'support' => 'Butuh akses? Hubungi administrator.',
        ],
        404 => [
            'message' => 'Halaman tidak tersedia.',
            'support' => 'Periksa alamat atau kembali ke beranda.',
        ],
        419 => [
            'message' => 'Sesi telah berakhir.',
            'support' => 'Muat ulang halaman, lalu coba lagi.',
        ],
        429 => [
            'message' => 'Terlalu banyak permintaan dalam waktu singkat.',
            'support' => 'Tunggu sebentar, lalu coba lagi.',
        ],
        500 => [
            'message' => 'Terjadi kesalahan di server.',
            'support' => 'Jika berulang, laporkan kode ini: {ref}',
        ],
        503 => [
            'message' => 'Layanan sedang dalam pemeliharaan.',
            'support' => 'Coba lagi dalam beberapa menit.',
        ],
    ],

    // Dipakai untuk kode status yang tidak ada di atas.
    'default' => [
        'message' => 'Permintaan tidak dapat diproses.',
        'support' => 'Jika berulang, laporkan kode ini: {ref}',
    ],

    'back_home' => 'Beranda',
    'go_back' => 'Kembali',
];
