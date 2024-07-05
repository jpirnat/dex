<?php
declare(strict_types=1);

use Jp\Dex\Domain\Evolutions\EvolutionFormatter;
use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/environment.php';
$container = require __DIR__ . '/../config/container.php';

/** @var EvolutionRepositoryInterface $evolutionRepository */
$evolutionRepository = $container->get(EvolutionRepositoryInterface::class);

/** @var EvolutionFormatter $evolutionFormatter */
$evolutionFormatter = $container->get(EvolutionFormatter::class);

$languageId = new LanguageId(LanguageId::ENGLISH);

$evolutions = $evolutionRepository->getAll();
$count = 0;
foreach ($evolutions as $evolution) {
	try {
		$text = $evolutionFormatter->format($evolution, $languageId);
		echo $text;
		echo "\n";
	} catch (Exception $e) {
		var_dump($evolution);
		throw $e;
	}

	$count++;
}

echo "Total tested: $count\n";
