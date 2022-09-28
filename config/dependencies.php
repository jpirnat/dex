<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function(ContainerConfigurator $configurator) {

$services = $configurator->services()
	->defaults()
		->autowire()
		->public();


// Databases

// Main database connection.
$host = $_SERVER['DB_HOST'];
$port = $_SERVER['DB_PORT'];
$name = $_SERVER['DB_NAME'];
$user = $_SERVER['DB_USER'];
$pass = $_SERVER['DB_PASS'];
$services->set(PDO::class)
	->class(PDO::class)
	->args([
		"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
		$user,
		$pass,
		[
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		],
	])
;

// Database setup connection.
$user = $_SERVER['DB_SETUP_USER'];
$pass = $_SERVER['DB_SETUP_PASS'];
$services->set('$dbsetup')
	->class(PDO::class)
	->args([
		"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
		$user,
		$pass,
		[
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_LOCAL_INFILE => true,
		],
	])
;


// Import almost everything.
$services->load('Jp\\Dex\\Application\\', '../src/Application')
	->exclude([
		'../src/Application/Models/BreedingChains/BreedingChainRecord.php',
		'../src/Application/Models/DexMove/DexMovePokemon.php',
		'../src/Application/Models/DexMove/DexMovePokemonMethod.php',
		'../src/Application/Models/DexPokemon/DexPokemonMove.php',
		'../src/Application/Models/DexPokemon/DexPokemonMoveMethod.php',
		'../src/Application/Models/StatsAveragedLeads/LeadsData.php',
		'../src/Application/Models/StatsAveragedPokemon/AbilityData.php',
		'../src/Application/Models/StatsAveragedPokemon/ItemData.php',
		'../src/Application/Models/StatsAveragedPokemon/MoveData.php',
		'../src/Application/Models/StatsAveragedUsage/UsageData.php',
		'../src/Application/Models/StatsPokemon/StatData.php',
	])
;

$services->load('Jp\\Dex\\Infrastructure\\', '../src/Infrastructure');
$services->load('Jp\\Dex\\Presentation\\', '../src/Presentation');

// Domain services
$services->set(\Jp\Dex\Domain\BreedingChains\BreedingChainFinder::class);
$services->set(\Jp\Dex\Domain\PokemonMoves\PokemonMoveFormatter::class);
$services->set(\Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\UsageTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\LeadUsageTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\MovesetAbilityTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\MovesetItemTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\MovesetMoveTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\UsageAbilityTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\UsageItemTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\UsageMoveTrendGenerator::class);
$services->set(\Jp\Dex\Domain\Calculators\StatCalculator::class);
$services->set(\Jp\Dex\Domain\Stats\Trends\Generators\TrendPointCalculator::class);

$services->load('Jp\\Dex\\Domain\\Import\\', '../src/Domain/Import')
	->exclude([
		'../src/Domain/Import/Extractors/Exceptions/*',
		'../src/Domain/Import/Showdown/*',
		'../src/Domain/Import/Structs/*',
	])
;

// Some other global classes we need
$services->set(\IntlDateFormatter::class)
	->class(\IntlDateFormatter::class)
	->args([null, null, null])
;
$services->set(\NumberFormatter::class)
	->class(\NumberFormatter::class)
	->args([null, null])
;


// Templating
$services->set(\Twig\Loader\FilesystemLoader::class)
	->arg('$paths', [__DIR__ . '/../templates'])
;

$services->alias(
	\Twig\Loader\LoaderInterface::class,
	\Twig\Loader\FilesystemLoader::class
);

$services->set(\Twig\Environment::class)
	->arg('$options', [
		'cache' => __DIR__ . '/../templates/cache',
		'auto_reload' => true,
	]);

$services->alias(
	\Jp\Dex\Presentation\RendererInterface::class,
	\Jp\Dex\Infrastructure\TwigRenderer::class
);


// Middleware
$services->set(\Jp\Dex\Application\Middleware\HtmlErrorMiddleware::class)
	->arg('$environment', $_SERVER['ENVIRONMENT'])
;
$services->set(\Jp\Dex\Application\Middleware\JsonErrorMiddleware::class)
	->arg('$environment', $_SERVER['ENVIRONMENT'])
;


// Interfaces
$services->alias(
	\Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseAbilityDescriptionRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseAbilityNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Abilities\AbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\BaseStatRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseBaseStatRepository::class
);
$services->alias(
	\Jp\Dex\Domain\BreedingChains\BreedingChainQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseBreedingChainQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexCategoryRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\DexMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Natures\DexNatureRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexNatureRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Abilities\DexPokemonAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexPokemonAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Types\DexTypeRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexTypeRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseDexVersionGroupRepository::class
);
$services->alias(
	\Jp\Dex\Domain\EggGroups\EggGroupNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseEggGroupNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Flags\FlagRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseFlagRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Formats\FormatRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseFormatRepository::class
);
$services->alias(
	\Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseFormIconRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseGenerationMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Versions\GenerationRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseGenerationRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseItemDescriptionRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Items\ItemNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseItemNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Items\ItemRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseItemRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Languages\LanguageNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLanguageNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Languages\LanguageRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLanguageRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLeadsAveragedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLeadsPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLeadsRatedAveragedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLeadsRatedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseLeadsRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Models\ModelRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseModelRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\MonthQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMonthQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMoveDescriptionRepository::class
);
$services->alias(
	\Jp\Dex\Domain\PokemonMoves\MoveMethodNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMoveMethodNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMoveMethodRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\MoveNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMoveNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\MoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedItemRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedAveragedMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedCounterRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedItemRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedSpreadRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseMovesetRatedTeammateRepository::class
);
$services->alias(
	\Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabasePokemonEggGroupRepository::class
);
$services->alias(
	\Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabasePokemonMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabasePokemonNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabasePokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabasePokemonTypeRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseRatingQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownFormatRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownItemRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownNatureRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\Showdown\DatabaseShowdownPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Species\SpeciesRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseSpeciesRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\StatNameRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatNameRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsAbilityPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\StatsChartQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsChartQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsItemPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Leads\StatsLeadsPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsLeadsPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Usage\StatsMovePokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsMovePokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Abilities\StatsPokemonAbilityRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonAbilityRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Counters\StatsPokemonCounterRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonCounterRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Items\StatsPokemonItemRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonItemRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Moves\StatsPokemonMoveRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonMoveRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Spreads\StatsPokemonSpreadRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonSpreadRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Teammates\StatsPokemonTeammateRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsPokemonTeammateRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseStatsUsagePokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Items\TmRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseTmRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseTypeMatchupRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Types\TypeRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseTypeRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageAveragedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsagePokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsageQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageRatedAveragedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageRatedPokemonRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsageRatedQueriesInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageRatedQueries::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageRatedRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseUsageRepository::class
);
$services->alias(
	\Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface::class,
	\Jp\Dex\Infrastructure\DatabaseVersionGroupRepository::class
);

};
