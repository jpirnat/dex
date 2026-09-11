<?php
declare(strict_types=1);

use Jp\Dex\Domain\Import\Champions\MoveFlagNameImporter;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/environment.php';
$container = require __DIR__ . '/../config/container.php';

/** @var MoveFlagNameImporter $moveFlagNameImporter */
$moveFlagNameImporter = $container->get(MoveFlagNameImporter::class);

$moveFlagNameImporter->import();
