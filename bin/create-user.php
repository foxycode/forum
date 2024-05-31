<?php declare(strict_types=1);

use App\Core\UserManager;

if (!isset($_SERVER['argv'][2])) {
    echo '
Add new user to database.

Usage: create-user.php <name> <password>
';
    exit(1);
}

[, $user, $password] = $_SERVER['argv'];

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType(UserManager::class)->add($user, $password);

echo "User $user was added.\n";
