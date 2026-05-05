<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

session_unset();
session_destroy();

redirect(frontend_url('index.php'));
