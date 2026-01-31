<?php

declare(strict_types=1);

/**
 * Run all migrations for the project.
 *
 * Usage: php scripts/run_migrations.php
 */

$root = dirname(__DIR__);
chdir($root);

passthru('php artisan migrate --force', $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Migration failed with exit code {$exitCode}.\n");
    exit($exitCode);
}

fwrite(STDOUT, "Migrations completed successfully.\n");
