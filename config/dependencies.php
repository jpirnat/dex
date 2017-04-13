<?php
declare(strict_types=1);

use Dice\Dice;
use Jp\Container\DiceContainer;
use Jp\Dex\Application\Models\AbilitiesModel;
use Jp\Dex\Application\Models\ItemsModel;
use Jp\Dex\Application\Models\LeadsModel;
use Jp\Dex\Application\Models\MovesModel;
use Jp\Dex\Application\Models\UsageModel;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Usage\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Usage\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Usage\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\Usage\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Infrastructure\DatabaseAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseFormatRepository;
use Jp\Dex\Infrastructure\DatabaseItemRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseMoveRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedItemRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedMoveRepository;
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
		AbilityRepositoryInterface::class => [
			'instance' => DatabaseAbilityRepository::class
		],
		FormatRepositoryInterface::class => [
			'instance' => DatabaseFormatRepository::class
		],
		ItemRepositoryInterface::class => [
			'instance' => DatabaseItemRepository::class
		],
		MoveRepositoryInterface::class => [
			'instance' => DatabaseMoveRepository::class
		],
		PokemonRepositoryInterface::class => [
			'instance' => DatabasePokemonRepository::class
		],
		LeadsRatedPokemonRepositoryInterface::class => [
			'instance' => DatabaseLeadsRatedPokemonRepository::class
		],
		MovesetRatedAbilityRepositoryInterface::class => [
			'instance' => DatabaseMovesetRatedAbilityRepository::class
		],
		MovesetRatedItemRepositoryInterface::class => [
			'instance' => DatabaseMovesetRatedItemRepository::class
		],
		MovesetRatedMoveRepositoryInterface::class => [
			'instance' => DatabaseMovesetRatedMoveRepository::class
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
$container->dice()->addRule(AbilitiesModel::class, $rule);
$container->dice()->addRule(ItemsModel::class, $rule);
$container->dice()->addRule(LeadsModel::class, $rule);
$container->dice()->addRule(MovesModel::class, $rule);
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
