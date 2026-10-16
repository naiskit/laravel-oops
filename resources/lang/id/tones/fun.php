<?php

return [
    'statuses' => [
        403 => [
            'message' => 'Eits, pintunya dikunci! Ruangan ini khusus tamu VIP, dan sepertinya namamu belum masuk daftar.',
            'support' => 'Merasa harusnya boleh masuk? Colek administrator, siapa tahu dikasih kunci.',
        ],
        404 => [
            'message' => 'Waduh, kamu nyasar! Halaman ini lagi main petak umpet, dan sejauh ini dia yang menang.',
            'support' => 'Yakin halamannya ada? Kabari administrator biar ikut bantu mencari.',
        ],
        419 => [
            'message' => 'Kelamaan ditinggal, sesimu keburu tidur pulas. Bangunkan dengan memuat ulang halaman, ya.',
            'support' => 'Muat ulang halaman dan coba lagi. Kalau masih ngambek, panggil administrator.',
        ],
        429 => [
            'message' => 'Wih, santai dulu! Jarimu cepat sekali, servernya sampai ngos-ngosan. Kasih dia napas sebentar.',
            'support' => 'Tunggu sebentar, lalu coba lagi. Kalau masih tertahan, panggil administrator.',
        ],
        500 => [
            'message' => 'Ups, servernya kesandung! Tenang, ini bukan salahmu. Dia lagi memunguti barang yang jatuh berserakan.',
            'support' => 'Kalau masih terjadi, kirim kode ini ke administrator biar biang keroknya cepat ketemu: {ref}',
        ],
        503 => [
            'message' => 'Kami sedang beres-beres supaya makin nyaman. Sebentar lagi pintu dibuka kembali!',
            'support' => 'Jadwal buka kembali akan diumumkan administrator. Sabar, ya!',
        ],
    ],

    // Used for any status code not listed above.
    'default' => [
        'message' => 'Hmm, sepertinya ada yang berjalan di luar skenario. Tenang, kami sedang mengusutnya.',
        'support' => 'Kalau masih terjadi, kirim kode ini ke administrator: {ref}',
    ],

    'back_home' => 'Pulang ke Beranda',
    'go_back' => 'Mundur Dulu',
];
