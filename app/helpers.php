<?php 

function url(string $path = ''): string {
    $baseUrl = $_ENV['BASE_URL'];
    return $baseUrl . '/' . ltrim($path, '/');
}