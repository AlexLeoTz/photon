<?php
echo "<h1>PHP SQLite3 Test</h1>";

echo "<h2>PHP Version:</h2>";
echo PHP_VERSION;

echo "<h2>Loaded Extensions:</h2>";
print_r(get_loaded_extensions());

echo "<h2>SQLite3 Class Check:</h2>";
echo class_exists('SQLite3') ? "SQLite3 class exists" : "SQLite3 class does NOT exist";

echo "<h2>PDO Drivers:</h2>";
print_r(PDO::getAvailableDrivers()); 