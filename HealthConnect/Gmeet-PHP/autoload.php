<?php
spl_autoload_register(function ($class_name) {
    $base_dir = __DIR__ . '/google-api-client/src/'; // Update this with the correct path to the "src" directory inside the extracted folder

    $class_file = $base_dir . str_replace('\\', '/', $class_name) . '.php';

    if (file_exists($class_file)) {
        require_once $class_file;
    }
});
