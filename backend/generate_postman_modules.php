<?php

$baseUrl = '{{base_url}}';
$outputDir = 'postman';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Common variables and auth structure
$baseCollection = [
    'info' => [
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        [
            'key' => 'base_url',
            'value' => 'http://127.0.0.1:8000',
            'type' => 'string'
        ],
        [
            'key' => 'token',
            'value' => '',
            'type' => 'string'
        ]
    ],
    'auth' => [
        'type' => 'bearer',
        'bearer' => [
            [
                'key' => 'token',
                'value' => '{{token}}',
                'type' => 'string'
            ]
        ]
    ]
];

// Special case for Auth
$authItems = [
    [
        'name' => 'Login Nhan Vien',
        'request' => [
            'method' => 'POST',
            'header' => [],
            'body' => [
                'mode' => 'raw',
                'raw' => json_encode(['email' => 'daotao@school.edu.vn', 'password' => 'password123'], JSON_PRETTY_PRINT),
                'options' => ['raw' => ['language' => 'json']]
            ],
            'url' => ['raw' => $baseUrl . '/api/login/nhan-vien'],
        ]
    ],
    [
        'name' => 'Login Sinh Vien',
        'request' => [
            'method' => 'POST',
            'header' => [],
            'body' => [
                'mode' => 'raw',
                'raw' => json_encode(['email' => 'student@school.edu.vn', 'password' => 'password123'], JSON_PRETTY_PRINT),
                'options' => ['raw' => ['language' => 'json']]
            ],
            'url' => ['raw' => $baseUrl . '/api/login/sinh-vien'],
        ]
    ]
];

saveCollection('Auth', $authItems);

// Other modules
$modules = [
    'chuc-vus' => ['search', 'allowed', 'status'],
    'chuc-nang' => ['only-get'],
    'phong-bans' => ['full'],
    'nhan-viens' => ['full', 'status'],
    'sinh-viens' => ['full'],
    'mon-hocs' => ['full'],
    'lop-hocs' => ['full'],
    'bang-diems' => ['full'],
    'chung-chis' => ['full'],
    'du-ans' => ['full'],
    'don-vi-caps' => ['full'],
];

foreach ($modules as $slug => $config) {
    $items = [];
    $moduleName = ucwords(str_replace('-', ' ', $slug));

    // Standard items based on synchronized naming convention
    $items[] = createRequest('Get Data', 'GET', "admin/$slug/get-data");

    if (!in_array('only-get', $config)) {
        $items[] = createRequest('Create', 'POST', "admin/$slug/create", ['key' => 'value']);
        
        if (in_array('full', $config)) {
            $items[] = createRequest('Detail', 'GET', "admin/$slug/detail/1");
        }
        
        $items[] = createRequest('Update', 'PUT', "admin/$slug/update/1", ['key' => 'updated']);
        $items[] = createRequest('Delete', 'DELETE', "admin/$slug/delete/1");
    }
    
    if (in_array('search', $config) || in_array('full', $config)) {
        $items[] = createRequest('Search', 'GET', "admin/$slug/search?keyword=test");
    }
    
    if (in_array('status', $config)) {
        $items[] = createRequest('Change Status', 'POST', "admin/$slug/change-status", ['id' => 1, 'trang_thai' => 1]);
    }
    
    if (in_array('allowed', $config)) {
        $items[] = createRequest('Get Data Allowed', 'GET', "admin/$slug/get-data-allowed");
    }

    saveCollection($moduleName, $items);
}

function createRequest($name, $method, $path, $body = null) {
    $req = [
        'name' => $name,
        'request' => [
            'method' => $method,
            'header' => [],
            'url' => [
                'raw' => '{{base_url}}/api/' . $path,
                'host' => ['{{base_url}}'],
                'path' => array_merge(['api'], explode('/', $path))
            ],
        ]
    ];
    if ($body) {
        $req['request']['body'] = [
            'mode' => 'raw',
            'raw' => json_encode($body, JSON_PRETTY_PRINT),
            'options' => ['raw' => ['language' => 'json']]
        ];
    }
    return $req;
}

function saveCollection($name, $items) {
    global $baseCollection, $outputDir;
    $collection = $baseCollection;
    $collection['info']['name'] = "DATN 2026 - $name";
    $collection['item'] = $items;
    
    $fileName = strtolower(str_replace(' ', '_', $name)) . ".json";
    file_put_contents("$outputDir/$fileName", json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Generated: $outputDir/$fileName\n";
}
