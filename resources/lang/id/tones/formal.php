<?php

return [
    'statuses' => [
        403 => [
            'message' => 'Mohon maaf, Anda belum memiliki izin untuk mengakses halaman ini.',
            'support' => 'Apabila Anda merasa memerlukan akses, silakan menghubungi administrator.',
        ],
        404 => [
            'message' => 'Mohon maaf, halaman yang Anda tuju tidak dapat kami temukan. Halaman tersebut mungkin telah dipindahkan atau tidak lagi tersedia.',
            'support' => 'Silakan periksa kembali alamat yang Anda tuju, atau kembali ke beranda.',
        ],
        419 => [
            'message' => 'Demi keamanan, sesi Anda telah berakhir karena halaman ini terbuka cukup lama.',
            'support' => 'Silakan muat ulang halaman, lalu coba kembali.',
        ],
        429 => [
            'message' => 'Mohon maaf, kami menerima terlalu banyak permintaan dalam waktu singkat.',
            'support' => 'Silakan tunggu beberapa saat sebelum mencoba kembali.',
        ],
        500 => [
            'message' => 'Mohon maaf atas ketidaknyamanan ini. Terjadi kendala pada sistem kami, dan kami sedang menanganinya.',
            'support' => 'Apabila kendala berlanjut, silakan menghubungi administrator dengan menyertakan kode referensi berikut: {ref}',
        ],
        503 => [
            'message' => 'Kami sedang melakukan pemeliharaan untuk meningkatkan layanan. Terima kasih atas kesabaran Anda.',
            'support' => 'Layanan akan segera tersedia kembali. Silakan coba beberapa saat lagi.',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'Mohon maaf, permintaan Anda belum dapat kami proses saat ini.',
        'support' => 'Apabila kendala berlanjut, silakan menghubungi administrator dengan menyertakan kode referensi berikut: {ref}',
    ],

    'back_home' => 'Kembali ke Beranda',
    'go_back' => 'Halaman Sebelumnya',
];
