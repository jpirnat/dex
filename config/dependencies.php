<?php
declare(strict_types=1);

use Dice\Dice;
use Jp\Container\DiceContainer;
use Jp\Dex\Application\Middleware\HtmlErrorMiddleware;
use Jp\Dex\Application\Models\AbilitiesModel;
use Jp\Dex\Application\Models\DateModel;
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
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
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
use Jp\Dex\Infrastructure\DatabaseFormIconRepository;
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

// Databases

// Main database connection.
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
$container->dice()->addRule(Twig_Loader_Filesystem::class, $rule);

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
$container->dice()->addRule(Twig_Environment::class, $rule);

// Middleware
$environment = getenv('ENVIRONMENT');
$rule = [
	'constructParams' => [
		$environment,
	]
];
$container->dice()->addRule(HtmlErrorMiddleware::class, $rule);


// Interfaces
$rule = ['instanceOf' => DatabaseAbilityNameRepository::class];
$container->dice()->addRule(AbilityNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseAbilityRepository::class];
$container->dice()->addRule(AbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseBaseStatRepository::class];
$container->dice()->addRule(BaseStatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseFormatRepository::class];
$container->dice()->addRule(FormatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseFormIconRepository::class];
$container->dice()->addRule(FormIconRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseItemNameRepository::class];
$container->dice()->addRule(ItemNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseItemRepository::class];
$container->dice()->addRule(ItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseLanguageNameRepository::class];
$container->dice()->addRule(LanguageNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseLanguageRepository::class];
$container->dice()->addRule(LanguageRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseLeadsPokemonRepository::class];
$container->dice()->addRule(LeadsPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseLeadsRatedPokemonRepository::class];
$container->dice()->addRule(LeadsRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseLeadsRepository::class];
$container->dice()->addRule(LeadsRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMoveNameRepository::class];
$container->dice()->addRule(MoveNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMoveRepository::class];
$container->dice()->addRule(MoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetPokemonRepository::class];
$container->dice()->addRule(MovesetPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedAbilityRepository::class];
$container->dice()->addRule(MovesetRatedAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedCounterRepository::class];
$container->dice()->addRule(MovesetRatedCounterRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedItemRepository::class];
$container->dice()->addRule(MovesetRatedItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedMoveRepository::class];
$container->dice()->addRule(MovesetRatedMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedPokemonRepository::class];
$container->dice()->addRule(MovesetRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedSpreadRepository::class];
$container->dice()->addRule(MovesetRatedSpreadRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseMovesetRatedTeammateRepository::class];
$container->dice()->addRule(MovesetRatedTeammateRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseNatureNameRepository::class];
$container->dice()->addRule(NatureNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseNatureRepository::class];
$container->dice()->addRule(NatureRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseNatureStatModifierRepository::class];
$container->dice()->addRule(NatureStatModifierRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabasePokemonNameRepository::class];
$container->dice()->addRule(PokemonNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabasePokemonRepository::class];
$container->dice()->addRule(PokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownAbilityRepository::class];
$container->dice()->addRule(ShowdownAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownFormatRepository::class];
$container->dice()->addRule(ShowdownFormatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownItemRepository::class];
$container->dice()->addRule(ShowdownItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownMoveRepository::class];
$container->dice()->addRule(ShowdownMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownNatureRepository::class];
$container->dice()->addRule(ShowdownNatureRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseShowdownPokemonRepository::class];
$container->dice()->addRule(ShowdownPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseUsagePokemonRepository::class];
$container->dice()->addRule(UsagePokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseUsageRatedPokemonRepository::class];
$container->dice()->addRule(UsageRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseUsageRatedRepository::class];
$container->dice()->addRule(UsageRatedRepositoryInterface::class, $rule);

$rule = ['instanceOf' => DatabaseUsageRepository::class];
$container->dice()->addRule(UsageRepositoryInterface::class, $rule);


// Models are shared between controllers and views.
$rule = [
	'shared' => true,
];
$container->dice()->addRule(AbilitiesModel::class, $rule);
$container->dice()->addRule(DateModel::class, $rule);
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
