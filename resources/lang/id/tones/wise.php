<?php

// Tenang dan reflektif: satu kutipan per status, lalu apa yang terjadi
// dan apa yang bisa dilakukan. Kutipan ditulis persis seperti aslinya:
// peribahasa Indonesia atau karya sastra Indonesia, bukan terjemahan.

return [
    'statuses' => [
        403 => [
            'quote' => [
                'text' => 'Di mana bumi dipijak, di situ langit dijunjung.',
                'author' => 'Peribahasa',
            ],
            'message' => 'Setiap tempat punya aturannya sendiri, dan halaman ini hanya terbuka bagi yang memiliki izin.',
            'support' => 'Jika Anda merasa seharusnya memiliki akses, sampaikan kepada administrator.',
        ],
        404 => [
            'quote' => [
                'text' => 'Malu bertanya, sesat di jalan.',
                'author' => 'Peribahasa',
            ],
            'message' => 'Halaman yang Anda cari tidak ada di sini. Mungkin sudah berpindah, atau alamatnya kurang tepat.',
            'support' => 'Jangan ragu bertanya kepada administrator, atau mulai lagi dari beranda.',
        ],
        419 => [
            'quote' => [
                'text' => 'Yang fana adalah waktu. Kita abadi.',
                'author' => 'Sapardi Djoko Damono',
            ],
            'message' => 'Waktu terus berjalan, dan sesi Anda telah berakhir karena halaman ini terbuka cukup lama.',
            'support' => 'Muat ulang halaman, lalu lanjutkan dari sana.',
        ],
        429 => [
            'quote' => [
                'text' => 'Biar lambat asal selamat.',
                'author' => 'Peribahasa',
            ],
            'message' => 'Terlalu banyak permintaan masuk dalam waktu singkat. Sistem perlu sedikit jeda.',
            'support' => 'Tunggu sejenak, lalu coba kembali.',
        ],
        500 => [
            'quote' => [
                'text' => 'Sepandai-pandai tupai melompat, akhirnya jatuh juga.',
                'author' => 'Peribahasa',
            ],
            'message' => 'Sistem kami sedang tergelincir, bukan karena Anda. Kami sedang membenahinya.',
            'support' => 'Jika kendala berlanjut, sampaikan kode berikut kepada administrator: {ref}',
        ],
        503 => [
            'quote' => [
                'text' => 'Berakit-rakit ke hulu, berenang-renang ke tepian.',
                'author' => 'Peribahasa',
            ],
            'message' => 'Kami sedang berbenah sebentar, agar layanan lebih baik setelah ini.',
            'support' => 'Layanan akan segera kembali. Silakan coba beberapa saat lagi.',
        ],
    ],

    // Dipakai untuk kode status yang tidak ada di atas.
    'default' => [
        'quote' => [
            'text' => 'Tak ada gading yang tak retak.',
            'author' => 'Peribahasa',
        ],
        'message' => 'Ada yang belum berjalan semestinya, dan kami sedang memperbaikinya.',
        'support' => 'Jika kendala berlanjut, sampaikan kode berikut kepada administrator: {ref}',
    ],

    'back_home' => 'Kembali ke Beranda',
    'go_back' => 'Halaman Sebelumnya',
];
