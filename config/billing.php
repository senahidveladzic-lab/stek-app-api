<?php

return [
    'subscription_type' => 'default',

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'For households up to 5 people',
            'prices' => [
                'monthly' => env('PADDLE_PRICE_STARTER_MONTHLY', 'pri_01kp3et8rxm4a1278pkfh3m4f9'),
                'yearly' => env('PADDLE_PRICE_STARTER_YEARLY', 'pri_01kp3et8zk77rx9vyhgkv7k3d8'),
            ],
        ],
        'max' => [
            'name' => 'Max',
            'description' => 'For larger households up to 10 people',
            'prices' => [
                'monthly' => env('PADDLE_PRICE_MAX_MONTHLY', 'pri_01kp3et95qk42jb0mmknx9cp7t'),
                'yearly' => env('PADDLE_PRICE_MAX_YEARLY', 'pri_01kp3et9crkbn1rxj9h6w8dn0v'),
            ],
        ],
    ],
];
