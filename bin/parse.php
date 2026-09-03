<?php
declare(strict_types=1);

use Jp\Dex\Domain\Import\SmogonStats\Downloaders\MonthDirectoryDownloader;
use Jp\Dex\Domain\Import\SmogonStats\Parsers\MonthDirectoryParser;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/environment.php';
$container = require __DIR__ . '/../config/container.php';

/** @var MonthDirectoryDownloader $downloader */
$downloader = $container->get(MonthDirectoryDownloader::class);

/** @var MonthDirectoryParser $parser */
$parser = $container->get(MonthDirectoryParser::class);


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
$parseMonth = $now->setDate($year, $month, 1);

$downloader->download($parseMonth);

$start = new DateTimeImmutable();

$parser->parse($parseMonth);

$end = new DateTimeImmutable();

$formats = $parser->getUnknownFormats();
$pokemons = $parser->getUnknownPokemon();
$abilities = $parser->getUnknownAbilities();
$items = $parser->getUnknownItems();
$natures = $parser->getUnknownNatures();
$moves = $parser->getUnknownMoves();
$types = $parser->getUnknownTypes();


// Display the output.
$startText = $start->format('Y-m-d H:i:s');
$endText = $end->format('Y-m-d H:i:s');

echo "Start Time: $startText\n";
echo "End Time: $endText\n";
echo "\n";

echo "Formats:\n";
foreach ($formats as $yearMonth => $monthFormats) {
    foreach ($monthFormats as $format) {
        echo "$yearMonth: $format\n";
    }
}
echo "\n";

echo "Pokémon:\n";
foreach ($pokemons as $pokemon) {
    echo "$pokemon\n";
}
echo "\n";

echo "Abilities:\n";
foreach ($abilities as $ability) {
    echo "$ability\n";
}
echo "\n";

echo "Items:\n";
foreach ($items as $item) {
    echo "$item\n";
}
echo "\n";

echo "Natures:\n";
foreach ($natures as $nature) {
    echo "$nature\n";
}
echo "\n";

echo "Moves:\n";
foreach ($moves as $move) {
    echo "$move\n";
}
echo "\n";

echo "Types:\n";
foreach ($types as $type) {
    echo "$type\n";
}
echo "\n";
