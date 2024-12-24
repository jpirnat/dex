<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\AveragedPokemonModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AveragedPokemonView
{
	public function __construct(
		private AveragedPokemonModel $averagedPokemonModel,
		private IntlFormatterFactory $formatterFactory,
		private DexFormatter $dexFormatter,
		private MonthControlFormatter $monthControlFormatter,
	) {}

	/**
	 * Set data for the stats averaged Pokémon page.
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->averagedPokemonModel->start;
		$end = $this->averagedPokemonModel->end;
		$format = $this->averagedPokemonModel->format;
		$rating = $this->averagedPokemonModel->rating;
		$pokemon = $this->averagedPokemonModel->pokemon;

		$formatter = $this->formatterFactory->createFor(
			$this->averagedPokemonModel->languageId
		);

		// Get the start and end months.
		$startMonth = new DateTime("$start-01");
		$endMonth   = new DateTime("$end-01");
		$startMonth = $this->monthControlFormatter->format($startMonth, $formatter);
		$endMonth   = $this->monthControlFormatter->format($endMonth,   $formatter);

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->averagedPokemonModel->pokemonModel;
		$dexPokemon = $pokemonModel->pokemon;
		$stats = $pokemonModel->stats;
		$versionGroup = $this->averagedPokemonModel->versionGroup;
		$generation = $this->averagedPokemonModel->generation;

		// Get abilities.
		$abilitiesData = $this->averagedPokemonModel->abilities;
		$abilities = [];
		foreach ($abilitiesData as $ability) {
			$abilities[] = [
				'identifier' => $ability['identifier'],
				'name' => $ability['name'],
				'percent' => $ability['percent'],
				'percentText' => $formatter->formatPercent($ability['percent']),
			];
		}

		// Get items.
		$itemsData = $this->averagedPokemonModel->items;
		$items = [];
		foreach ($itemsData as $item) {
			$items[] = [
				'identifier' => $item['identifier'],
				'name' => $item['name'],
				'percent' => $item['percent'],
				'percentText' => $formatter->formatPercent($item['percent']),
			];
		}

		// Get moves.
		$movesData = $this->averagedPokemonModel->moves;
		$moves = [];
		foreach ($movesData as $move) {
			$moves[] = [
				'identifier' => $move['identifier'],
				'name' => $move['name'],
				'percent' => $move['percent'],
				'percentText' => $formatter->formatPercent($move['percent']),
			];
		}

		$sortByPercent = function (array $a, array $b) : int {
			return $b['percent'] <=> $a['percent'];
		};
		usort($abilities, $sortByPercent);
		usort($items, $sortByPercent);
		usort($moves, $sortByPercent);


		// Navigation breadcrumbs.
		$formatIdentifier = $format->identifier;
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'text' => 'Formats',
		], [
			'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
			'text' => $format->name,
		], [
			'text' => $dexPokemon->name,
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $startMonth['name'] . ' through '
					. $endMonth['name'] . ' ' . $format->name . ' - ' . $dexPokemon->name,
				'format' => [
					'identifier' => $format->identifier,
					'smogonDexIdentifier' => $format->smogonDexIdentifier,
					'fieldSize' => $format->fieldSize,
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->identifier,
					'name' => $dexPokemon->name,
					'sprite' => $dexPokemon->sprite,
					'types' => $this->dexFormatter->formatDexTypes($dexPokemon->types),
					'baseStats' => $dexPokemon->baseStats,
					'bst' => $dexPokemon->bst,
					'smogonDexIdentifier' => $pokemon->smogonDexIdentifier,
				],
				'stats' => $stats,

				'breadcrumbs' => $breadcrumbs,
				'startMonth' => $startMonth,
				'endMonth' => $endMonth,
				'ratings' => $this->averagedPokemonModel->ratings,

				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],
				'generation' => [
					'smogonDexIdentifier' => $generation->smogonDexIdentifier,
				],

				// The main data.
				'showAbilities' => $versionGroup->hasAbilities,
				'showItems' => $versionGroup->id->hasHeldItems(),
				'abilities' => $abilities,
				'items' => $items,
				'moves' => $moves,
			]
		]);
	}
}
