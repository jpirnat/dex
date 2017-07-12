<?php
declare(strict_types=1);

use Dice\Dice;
use Jp\Container\DiceContainer;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Models\AbilitiesModel;
use Jp\Dex\Application\Models\ErrorModel;
use Jp\Dex\Application\Models\ItemsModel;
use Jp\Dex\Application\Models\LanguageModel;
use Jp\Dex\Application\Models\LeadsModel;
use Jp\Dex\Application\Models\LeadsMonth\LeadsMonthModel;
use Jp\Dex\Application\Models\MovesetPokemonMonth\MovesetPokemonMonthModel;
use Jp\Dex\Application\Models\MovesModel;
use Jp\Dex\Application\Models\NotFoundModel;
use Jp\Dex\Application\Models\UsageModel;
use Jp\Dex\Application\Models\UsageMonth\UsageMonthModel;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureStatModifierRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;
use Jp\Dex\Infrastructure\DatabaseAbilityNameRepository;
use Jp\Dex\Infrastructure\DatabaseAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseBaseStatRepository;
use Jp\Dex\Infrastructure\DatabaseFormatRepository;
use Jp\Dex\Infrastructure\DatabaseItemNameRepository;
use Jp\Dex\Infrastructure\DatabaseItemRepository;
use Jp\Dex\Infrastructure\DatabaseLanguageNameRepository;
use Jp\Dex\Infrastructure\DatabaseLanguageRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseLeadsRepository;
use Jp\Dex\Infrastructure\DatabaseMoveNameRepository;
use Jp\Dex\Infrastructure\DatabaseMoveRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedAbilityRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedCounterRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedItemRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedMoveRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedSpreadRepository;
use Jp\Dex\Infrastructure\DatabaseMovesetRatedTeammateRepository;
use Jp\Dex\Infrastructure\DatabaseNatureNameRepository;
use Jp\Dex\Infrastructure\DatabaseNatureRepository;
use Jp\Dex\Infrastructure\DatabaseNatureStatModifierRepository;
use Jp\Dex\Infrastructure\DatabasePokemonNameRepository;
use Jp\Dex\Infrastructure\DatabasePokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsagePokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRatedRepository;
use Jp\Dex\Infrastructure\DatabaseUsageRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownAbilityRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownFormatRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownItemRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownMoveRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownNatureRepository;
use Jp\Dex\Infrastructure\Showdown\DatabaseShowdownPokemonRepository;

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
		__DIR__ . '/../templates',
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
			'cache' => __DIR__ . '/../templates/cache',
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
		AbilityNameRepositoryInterface::class => ['instance' => DatabaseAbilityNameRepository::class],
		AbilityRepositoryInterface::class => ['instance' => DatabaseAbilityRepository::class],
		BaseStatRepositoryInterface::class => ['instance' => DatabaseBaseStatRepository::class],
		FormatRepositoryInterface::class => ['instance' => DatabaseFormatRepository::class],
		ItemNameRepositoryInterface::class => ['instance' => DatabaseItemNameRepository::class],
		ItemRepositoryInterface::class => ['instance' => DatabaseItemRepository::class],
		LanguageNameRepositoryInterface::class => ['instance' => DatabaseLanguageNameRepository::class],
		LanguageRepositoryInterface::class => ['instance' => DatabaseLanguageRepository::class],
		LeadsPokemonRepositoryInterface::class => ['instance' => DatabaseLeadsPokemonRepository::class],
		LeadsRatedPokemonRepositoryInterface::class => ['instance' => DatabaseLeadsRatedPokemonRepository::class],
		LeadsRepositoryInterface::class => ['instance' => DatabaseLeadsRepository::class],
		MoveNameRepositoryInterface::class => ['instance' => DatabaseMoveNameRepository::class],
		MoveRepositoryInterface::class => ['instance' => DatabaseMoveRepository::class],
		MovesetPokemonRepositoryInterface::class => ['instance' => DatabaseMovesetPokemonRepository::class],
		MovesetRatedAbilityRepositoryInterface::class => ['instance' => DatabaseMovesetRatedAbilityRepository::class],
		MovesetRatedCounterRepositoryInterface::class => ['instance' => DatabaseMovesetRatedCounterRepository::class],
		MovesetRatedItemRepositoryInterface::class => ['instance' => DatabaseMovesetRatedItemRepository::class],
		MovesetRatedMoveRepositoryInterface::class => ['instance' => DatabaseMovesetRatedMoveRepository::class],
		MovesetRatedPokemonRepositoryInterface::class => ['instance' => DatabaseMovesetRatedPokemonRepository::class],
		MovesetRatedSpreadRepositoryInterface::class => ['instance' => DatabaseMovesetRatedSpreadRepository::class],
		MovesetRatedTeammateRepositoryInterface::class => ['instance' => DatabaseMovesetRatedTeammateRepository::class],
		NatureNameRepositoryInterface::class => ['instance' => DatabaseNatureNameRepository::class],
		NatureRepositoryInterface::class => ['instance' => DatabaseNatureRepository::class],
		NatureStatModifierRepositoryInterface::class => ['instance' => DatabaseNatureStatModifierRepository::class],
		PokemonNameRepositoryInterface::class => ['instance' => DatabasePokemonNameRepository::class],
		PokemonRepositoryInterface::class => ['instance' => DatabasePokemonRepository::class],
		ShowdownAbilityRepositoryInterface::class => ['instance' => DatabaseShowdownAbilityRepository::class],
		ShowdownFormatRepositoryInterface::class => ['instance' => DatabaseShowdownFormatRepository::class],
		ShowdownItemRepositoryInterface::class => ['instance' => DatabaseShowdownItemRepository::class],
		ShowdownMoveRepositoryInterface::class => ['instance' => DatabaseShowdownMoveRepository::class],
		ShowdownNatureRepositoryInterface::class => ['instance' => DatabaseShowdownNatureRepository::class],
		ShowdownPokemonRepositoryInterface::class => ['instance' => DatabaseShowdownPokemonRepository::class],
		UsagePokemonRepositoryInterface::class => ['instance' => DatabaseUsagePokemonRepository::class],
		UsageRatedPokemonRepositoryInterface::class => ['instance' => DatabaseUsageRatedPokemonRepository::class],
		UsageRatedRepositoryInterface::class => ['instance' => DatabaseUsageRatedRepository::class],
		UsageRepositoryInterface::class => ['instance' => DatabaseUsageRepository::class],
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
$container->dice()->addRule(LanguageModel::class, $rule);
$container->dice()->addRule(LeadsModel::class, $rule);
$container->dice()->addRule(LeadsMonthModel::class, $rule);
$container->dice()->addRule(MovesetPokemonMonthModel::class, $rule);
$container->dice()->addRule(MovesModel::class, $rule);
$container->dice()->addRule(NotFoundModel::class, $rule);
$container->dice()->addRule(UsageModel::class, $rule);
$container->dice()->addRule(UsageMonthModel::class, $rule);

// Shared repositories
$rule = [
	'shared' => true,
];
$container->dice()->addRule(ShowdownAbilityRepositoryInterface::class, $rule);
$container->dice()->addRule(ShowdownFormatRepositoryInterface::class, $rule);
$container->dice()->addRule(ShowdownItemRepositoryInterface::class, $rule);
$container->dice()->addRule(ShowdownMoveRepositoryInterface::class, $rule);
$container->dice()->addRule(ShowdownNatureRepositoryInterface::class, $rule);
$container->dice()->addRule(ShowdownPokemonRepositoryInterface::class, $rule);

return $container;
