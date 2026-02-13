<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'bagops_db',
        'user' => 'root',
        'pass' => 'root',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '/bagops',
        'timezone' => 'Asia/Jakarta',
        'debug' => true, // Set true untuk mode pengembangan
    ],
    'error_reporting' => E_ALL, // Laporkan semua jenis error
    'display_errors' => 1,      // Tampilkan error di layar
    'log_errors' => 1,          // Aktifkan logging error
    'error_log' => __DIR__ . '/../storage/logs/error.log', // Lokasi file log
];
