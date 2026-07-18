<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200'),
    'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'banksampah'),
    'timeout' => env('ELASTICSEARCH_TIMEOUT', 2),
];
