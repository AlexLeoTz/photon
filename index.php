<?php

require_once __DIR__ . '/src/SimpleWire.php';
require_once __DIR__ . '/src/Database.php';

use SimpleWire\SimpleWire;

$wire = new SimpleWire();
$currentPage = $_GET['page'] ?? 'counter';
?>

<!DOCTYPE html>
<html>
<head>
    <title>SimpleWire Demo</title>
    <style>
        .nav {
            background: #f8f9fa;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .nav a {
            margin-right: 1rem;
            text-decoration: none;
            color: #007bff;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .nav a.active {
            color: #0056b3;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a
            href="?page=counter"
            wire:navigate="counter"
            <?php echo $currentPage === 'counter' ? 'class="active"' : ''; ?>
        >
            Counter Demo
        </a>
        <a
            href="?page=contact-form"
            wire:navigate="contact-form"
            <?php echo $currentPage === 'contact-form' ? 'class="active"' : ''; ?>
        >
            Contact Form
        </a>
    </nav>

    <main wire:content>
        <?php echo $wire->render($currentPage); ?>
    </main>

    <script src="./src/simple-wire.js"></script>
</body>
</html>