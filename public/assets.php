<?php
if ($_SERVER['REQUEST_URI'] === '/assets/simple-wire.js') {
    header('Content-Type: application/javascript');
    readfile(__DIR__ . '/../src/assets/simple-wire.js');
    exit;
} 