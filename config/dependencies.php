<?php
declare(strict_types=1);

use Dice\Dice;
use Jp\Container\DiceContainer;
use Jp\Dex\Application\Models\UsageModel;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Infrastructure\DatabaseFormatRepository;
use Jp\Dex\Infrastructure\DatabasePokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonRepository;
use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;

$dice = new Dice();
$container = new DiceContainer($dice);

// PDO
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$rule = [
	'constructParams' => [
		"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
		$user,
		$pass,
	],
	'shared' => true,
	'call' => [
		['setAttribute', [PDO::ATTR_EMULATE_PREPARES, false]],
		['setAttribute', [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]],
	],
];
$container->dice()->addRule(PDO::class, $rule);

// Repositories
$rule = [
	'substitutions' => [
		FormatRepositoryInterface::class => [
			'instance' => DatabaseFormatRepository::class
		],
		PokemonRepositoryInterface::class => [
			'instance' => DatabasePokemonRepository::class
		],
		UsageRatedPokemonRepositoryInterface::class => [
			'instance' => DatabaseUsageRatedPokemonRepository::class
		],
	],
];
$container->dice()->addRule('*', $rule);

// Models are shared between controllers and views.
$rule = [
	'shared' => true,
];
$container->dice()->addRule(UsageModel::class, $rule);

// Shared repositories
$rule = [
	'shared' => true,
];
$container->dice()->addRule(ShowdownAbilityRepository::class, $rule);
$container->dice()->addRule(ShowdownFormatRepository::class, $rule);
$container->dice()->addRule(ShowdownItemRepository::class, $rule);
$container->dice()->addRule(ShowdownMoveRepository::class, $rule);
$container->dice()->addRule(ShowdownNatureRepository::class, $rule);
$container->dice()->addRule(ShowdownPokemonRepository::class, $rule);

return $container;
