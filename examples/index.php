<?php

use SimpleWire\SimpleWire;
use SimpleWire\Database;

require_once __DIR__ . '/../vendor/autoload.php';

$wire = new SimpleWire();
$currentPage = $_GET['page'] ?? 'counter';
?>
<!DOCTYPE html>
<html>
<head>
    <title>SimpleWire Demo</title>
    <style>
        /* ... styles ... */
    </style>
</head>
<body>
    <nav class="nav">
        <!-- ... navigation ... -->
    </nav>

    <main wire:content>
        <?php echo $wire->render($currentPage); ?>
    </main>

    <script>
        <?php echo SimpleWire::getJavaScript(); ?>
    </script>
</body>
</html> 