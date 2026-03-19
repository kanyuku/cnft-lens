<?php

return [
    'blockfrost' => [
        'project_id' => env('BLOCKFROST_PROJECT_ID'),
        'network' => env('BLOCKFROST_NETWORK', 'mainnet'),
        'base_url' => env('BLOCKFROST_NETWORK') === 'preprod' 
            ? 'https://cardano-preprod.blockfrost.io/api/v0' 
            : 'https://cardano-mainnet.blockfrost.io/api/v0',
    ],
];
