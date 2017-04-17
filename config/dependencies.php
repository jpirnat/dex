<?php
declare(strict_types=1);

use Dice\Dice;
use Jp\Container\DiceContainer;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Models\AbilitiesModel;
use Jp\Dex\Application\Models\ErrorModel;
use Jp\Dex\Application\Models\ItemsModel;
use Jp\Dex\Application\Models\LeadsModel;
use Jp\Dex\Application\Models\MovesModel;
use Jp\Dex\Application\Models\NotFoundModel;
use Jp\Dex\Application\Models\UsageModel;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Domain\Stats\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\Stats\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;
use Jp\Dex\Infrastructure\DatabaseAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseFormatRepository;
use Jp\Dex\Infrastructure\DatabaseItemRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsRepository;
use Jp\Dex\Infrastructure\DatabaseMoveRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedItemRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedMoveRepository;
use Jp\Dex\Infrastructure\DatabasePokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsagePokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRatedRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRepository;
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


// Twig
$rule = [
	'constructParams' => [
		__DIR__ . '/../resources/templates',
	],
	'shared' => true,
];
$dice->addRule(Twig_Loader_Filesystem::class, $rule);

$rule = [
	'substitutions' => [
		Twig_LoaderInterface::class => [
			'instance' => Twig_Loader_Filesystem::class
		],
	],
	'shared' => true,
	'constructParams' => [
		[
			'cache' => __DIR__ . '/../resources/templates/cache',
			'auto_reload' => true,
		]
	],
];
$dice->addRule(Twig_Environment::class, $rule);

// Middleware
$environment = getenv('ENVIRONMENT');
$rule = [
	'constructParams' => [
		$environment,
	]
];
$container->dice()->addRule(HtmlErrorMiddleware::class, $rule);


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
		LeadsPokemonRepositoryInterface::class => [
			'instance' => DatabaseLeadsPokemonRepository::class
		],
		LeadsRatedPokemonRepositoryInterface::class => [
			'instance' => DatabaseLeadsRatedPokemonRepository::class
		],
		LeadsRepositoryInterface::class => [
			'instance' => DatabaseLeadsRepository::class
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
		UsagePokemonRepositoryInterface::class => [
			'instance' => DatabaseUsagePokemonRepository::class
		],
		UsageRatedPokemonRepositoryInterface::class => [
			'instance' => DatabaseUsageRatedPokemonRepository::class
		],
		UsageRatedRepositoryInterface::class => [
			'instance' => DatabaseUsageRatedRepository::class
		],
		UsageRepositoryInterface::class => [
			'instance' => DatabaseUsageRepository::class
		],
	],
];
$container->dice()->addRule('*', $rule);

// Models are shared between controllers and views.
$rule = [
	'shared' => true,
];
$container->dice()->addRule(AbilitiesModel::class, $rule);
$container->dice()->addRule(ErrorModel::class, $rule);
$container->dice()->addRule(ItemsModel::class, $rule);
$container->dice()->addRule(LeadsModel::class, $rule);
$container->dice()->addRule(MovesModel::class, $rule);
$container->dice()->addRule(NotFoundModel::class, $rule);
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
