<?php

// Untuk admin panel dan tools tim IT atau developer: arti status dan apa
// yang perlu dicek. Hanya teks statis, tidak pernah detail exception;
// penyebab aslinya ada di log aplikasi dengan kode referensi.

return [
    'statuses' => [
        403 => [
            'message' => 'Request sudah terautentikasi tetapi tidak terotorisasi. User saat ini tidak memiliki role atau permission yang dibutuhkan route ini.',
            'support' => 'Periksa role user dan policy atau gate untuk route ini.',
        ],
        404 => [
            'message' => 'Tidak ada route atau resource yang cocok dengan URL ini. Route mungkin tidak ada, atau data yang dituju tidak ditemukan.',
            'support' => 'Periksa URL, definisi route, dan pastikan ID yang diminta memang ada.',
        ],
        419 => [
            'message' => 'Token CSRF tidak ada atau sudah tidak valid, biasanya karena sesi berakhir sebelum form dikirim.',
            'support' => 'Muat ulang halaman untuk mendapatkan token baru. Jika terus terjadi, periksa lifetime dan driver session.',
        ],
        429 => [
            'message' => 'Batas rate limit untuk route ini terlampaui. Request berikutnya ditolak sampai jendela limit direset.',
            'support' => 'Tunggu sampai limit direset, atau tinjau rate limiter yang dipasang di route ini.',
        ],
        500 => [
            'message' => 'Terjadi exception yang tidak tertangani saat memproses request. Detail lengkapnya ada di log aplikasi.',
            'support' => 'Cari kode referensi ini di log aplikasi: {ref}',
        ],
        503 => [
            'message' => 'Aplikasi sedang dalam maintenance mode atau sementara tidak tersedia.',
            'support' => 'Tunggu maintenance selesai, atau jalankan "php artisan up" jika seharusnya sudah aktif.',
        ],
    ],

    // Dipakai untuk kode status yang tidak ada di atas.
    'default' => [
        'message' => 'Request berakhir dengan HTTP error. Detail lengkapnya ada di log aplikasi.',
        'support' => 'Cari kode referensi ini di log aplikasi: {ref}',
    ],

    'back_home' => 'Beranda',
    'go_back' => 'Kembali',
];
