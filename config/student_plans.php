<?php

return [
    'currency' => 'TRY',
    // Reference "list" price per lesson (for showing package discounts)
    'list_price_per_lesson' => 2000,
    'default_lesson_duration' => 40,
    'plans' => [
        'plan_3m' => [
            'key' => 'plan_3m',
            'title' => 'CORE STARTER',
            'display_title' => '🥉 CORE STARTER',
            'duration_months' => 3,
            'lesson_duration' => 40,
            'lessons_total' => 24,
            'cancel_total' => 1,
            'old_price' => 48000,
            'price' => 36000,
            'label' => null,
            'featured' => false,
        ],
        'plan_6m' => [
            'key' => 'plan_6m',
            'title' => 'PROGRESS BUILDER',
            'display_title' => '🥈 PROGRESS BUILDER',
            'duration_months' => 6,
            'lesson_duration' => 40,
            'lessons_total' => 48,
            'cancel_total' => 2,
            'old_price' => 96000,
            'price' => 48000,
            'label' => 'En Çok Tercih Edilen',
            'featured' => true,
            'subtitle' => 'En çok tercih edilen gelişim paketi',
        ],
        'plan_12m' => [
            'key' => 'plan_12m',
            'title' => 'PREMIUM PAKET',
            'display_title' => '🥇 PREMIUM PAKET ⭐',
            'duration_months' => 12,
            'lesson_duration' => 40,
            'lessons_total' => 96,
            'cancel_total' => 4,
            'old_price' => 192000,
            'price' => 67200,
            'label' => 'En İyi Değer',
            'featured' => true,
            'subtitle' => 'En iyi değer – En yüksek indirim',
            'tagline' => "En düşük ders başı ücret + maksimum ilerleme.\nGerçek dönüşüm isteyenler için.",
        ],
    ],
];

