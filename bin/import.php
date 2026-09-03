<?php
declare(strict_types=1);

use Jp\Dex\Domain\Import\SmogonStats\Downloaders\MonthDirectoryDownloader;
use Jp\Dex\Domain\Import\SmogonStats\Importers\MonthDirectoryImporter;
use Jp\Dex\Domain\Import\SmogonStats\ZygardeFixer;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/environment.php';
$container = require __DIR__ . '/../config/container.php';

/** @var MonthDirectoryDownloader $downloader */
$downloader = $container->get(MonthDirectoryDownloader::class);

/** @var MonthDirectoryImporter $importer */
$importer = $container->get(MonthDirectoryImporter::class);

/** @var ZygardeFixer $zygardeFixer */
$zygardeFixer = $container->get(ZygardeFixer::class);


// Get year and month from command line arguments.
$options = getopt('', ['year:', 'month:']);
$year = (int) ($options['year'] ?? '');
$month = (int) ($options['month'] ?? '');
if (!$year) {
    echo "Missing year parameter.\n";
    return;
}
if (!$month) {
    echo "Missing month parameter.\n";
    return;
}

$now = new DateTimeImmutable();
$importMonth = $now->setDate($year, $month, 1);

// $downloader->download($yearMonth);
// The download can be disabled if we always run the parse first.

$start = new DateTimeImmutable();

$importer->import($importMonth);
$zygardeFixer->fixZygarde();

$end = new DateTimeImmutable();


// Display the output.
$startText = $start->format('Y-m-d H:i:s');
$endText = $end->format('Y-m-d H:i:s');

echo "Start Time: $startText\n";
echo "End Time: $endText\n";
echo "\n";
