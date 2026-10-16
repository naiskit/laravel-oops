<?php

return [
    'statuses' => [
        403 => [
            'message' => 'Sepertinya kamu tidak punya izin untuk membuka halaman ini.',
            'support' => 'Kalau kamu merasa harusnya punya akses, kabari administrator, ya.',
        ],
        404 => [
            'message' => 'Halaman yang kamu cari sepertinya sudah pindah, atau memang tidak pernah ada.',
            'support' => 'Kalau kamu yakin halaman ini harusnya ada, kabari administrator, ya.',
        ],
        419 => [
            'message' => 'Halaman ini sudah terlalu lama dibuka. Coba muat ulang, ya.',
            'support' => 'Muat ulang halaman, lalu coba lagi. Kalau masih bermasalah, kabari administrator.',
        ],
        429 => [
            'message' => 'Pelan-pelan saja. Tarik napas sebentar, lalu coba lagi.',
            'support' => 'Tunggu sebentar, lalu coba lagi. Kalau masih bermasalah, kabari administrator.',
        ],
        500 => [
            'message' => 'Bukan salahmu, kok. Tim kami sedang membereskannya.',
            'support' => 'Kalau masalahnya masih muncul, kabari administrator dan sertakan kode ini: {ref}',
        ],
        503 => [
            'message' => 'Kami sedang melakukan sedikit perbaikan. Sebentar lagi kembali normal.',
            'support' => 'Nanti administrator akan kabari kapan layanan kembali normal.',
        ],
    ],
    'default' => [
        'message' => 'Ada sesuatu yang tidak berjalan semestinya.',
        'support' => 'Kalau masalahnya masih muncul, kabari administrator dan sertakan kode ini: {ref}',
    ],
    'back_home' => 'Kembali ke Beranda',
    'go_back' => 'Balik',
];
