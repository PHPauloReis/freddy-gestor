<?php

use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection([
    'dbname'   => $_ENV['DATABASE_NAME'] ?? 'freddy_db',
    'user'     => $_ENV['DATABASE_USER'] ?? 'freddy_user',
    'password' => $_ENV['DATABASE_PASSWORD'] ?? 'freddy_pass',
    'host'     => $_ENV['DATABASE_HOST'] ?? 'localhost',
    'driver'   => $_ENV['DATABASE_DRIVER'] ?? 'pdo_mysql',
    'charset'  => $_ENV['DATABASE_CHARSET'] ?? 'utf8mb4',
]);
