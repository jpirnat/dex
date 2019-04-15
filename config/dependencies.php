<?php
declare(strict_types=1);


/** @var \Dice\Dice $dice */
$dice = new \Dice\Dice();


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
		[
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		]
	],
	'shared' => true,
];
$dice = $dice->addRule(PDO::class, $rule);

// Database setup connection.
$user = getenv('DB_SETUP_USER');
$pass = getenv('DB_SETUP_PASS');
$rule = [
	'instanceOf' => PDO::class,
	'constructParams' => [
		"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
		$user,
		$pass,
		[
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_LOCAL_INFILE => true,
		]
	],
	'shared' => true,
];
$dice = $dice->addRule('$dbsetup', $rule);


// Templating
$rule = [
	'constructParams' => [
		__DIR__ . '/../templates',
	],
	'shared' => true,
];
$dice = $dice->addRule(\Twig\Loader\FilesystemLoader::class, $rule);

$rule = ['instanceOf' => \Twig\Loader\FilesystemLoader::class];
$dice = $dice->addRule(\Twig\Loader\LoaderInterface::class, $rule);

$rule = [
	'shared' => true,
	'constructParams' => [
		[
			'cache' => __DIR__ . '/../templates/cache',
			'auto_reload' => true,
		]
	],
];
$dice = $dice->addRule(\Twig\Environment::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\TwigRenderer::class];
$dice = $dice->addRule(\Jp\Dex\Presentation\RendererInterface::class, $rule);


// Middleware
$environment = getenv('ENVIRONMENT');
$rule = [
	'constructParams' => [
		$environment,
	]
];
$dice = $dice->addRule(\Jp\Dex\Application\Middleware\AjaxErrorMiddleware::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Middleware\HtmlErrorMiddleware::class, $rule);


// Interfaces
$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseAbilityDescriptionRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseAbilityNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Abilities\AbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseBaseStatRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\BaseStatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseBreedingChainQueries::class];
$dice = $dice->addRule(\Jp\Dex\Domain\BreedingChains\BreedingChainQueriesInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseCategoryRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Categories\CategoryRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseDexMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Moves\DexMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseDexPokemonAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Abilities\DexPokemonAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseDexTypeRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Types\DexTypeRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseEggGroupNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\EggGroups\EggGroupNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseFormatNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Formats\FormatNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseFormatRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Formats\FormatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseFormIconRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseGenerationMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseGenerationRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Versions\GenerationRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseItemDescriptionRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseItemNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Items\ItemNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseItemRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Items\ItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLanguageNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLanguageRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Languages\LanguageRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLeadsAveragedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLeadsPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLeadsRatedAveragedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLeadsRatedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseLeadsRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseModelRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Models\ModelRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMonthQueries::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMoveDescriptionRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMoveMethodNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMoveMethodRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMoveNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Moves\MoveNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Moves\MoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedItemRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedCounterRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedItemRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedSpreadRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseMovesetRatedTeammateRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseNatureNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Natures\NatureNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseNatureRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Natures\NatureRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonEggGroupRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabasePokemonTypeRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseRatingQueries::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownFormatRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownItemRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownNatureRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownNatureRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\Showdown\DatabaseShowdownPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseSpeciesRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Species\SpeciesRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseStatNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\StatNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseTmRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Items\TmRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseTypeEffectivenessRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Types\TypeEffectivenessRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseTypeIconRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseTypeNameRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Types\TypeNameRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseTypeRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Types\TypeRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageAveragedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsagePokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageQueries::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedAveragedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonAbilityRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonAbilityRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonItemRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItemRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonMoveRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMoveRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedQueries::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRatedRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseUsageRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface::class, $rule);

$rule = ['instanceOf' => \Jp\Dex\Infrastructure\DatabaseVersionGroupRepository::class];
$dice = $dice->addRule(\Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface::class, $rule);


// Models are shared between controllers and views.
$rule = [
	'shared' => true,
];
$dice = $dice->addRule(\Jp\Dex\Application\Models\BaseModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DateModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexAbilitiesModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexAbilityModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexMove\DexMoveModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexNaturesModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexPokemon\DexPokemonModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexTypeModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\DexTypesModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\ErrorModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\NotFoundModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsAbility\StatsAbilityModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsAveragedLeads\StatsAveragedLeadsModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsAveragedPokemon\StatsAveragedPokemonModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsAveragedUsage\StatsAveragedUsageModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsIndexModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsItem\StatsItemModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsLeads\StatsLeadsModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsMonth\StatsMonthModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsMove\StatsMoveModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\StatsUsage\StatsUsageModel::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Application\Models\TrendChartModel::class, $rule);

// Shared repositories
$rule = [
	'shared' => true,
];
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownAbilityRepositoryInterface::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownItemRepositoryInterface::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownMoveRepositoryInterface::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownNatureRepositoryInterface::class, $rule);
$dice = $dice->addRule(\Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface::class, $rule);

return new \Jp\Container\DiceContainer($dice);
