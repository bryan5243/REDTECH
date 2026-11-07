<?php

declare(strict_types=1);

define('PUBLIC_DIR', dirname(__DIR__));
require __DIR__ . '/../../src/admin.php';

check_post();
unset($_SESSION['admin'], $_SESSION['admin_seen']);
session_regenerate_id(true);
redirect('index.php');
