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
		private RendererInterface $renderer,
		private BaseView $baseView,
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
		$start = $this->averagedPokemonModel->getStart();
		$end = $this->averagedPokemonModel->getEnd();
		$format = $this->averagedPokemonModel->getFormat();
		$rating = $this->averagedPokemonModel->getRating();
		$pokemon = $this->averagedPokemonModel->getPokemon();

		$formatter = $this->formatterFactory->createFor(
			$this->averagedPokemonModel->getLanguageId()
		);

		// Get the start and end months.
		$startMonth = new DateTime("$start-01");
		$endMonth   = new DateTime("$end-01");
		$startMonth = $this->monthControlFormatter->format($startMonth, $formatter);
		$endMonth   = $this->monthControlFormatter->format($endMonth,   $formatter);

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->averagedPokemonModel->getPokemonModel();
		$dexPokemon = $pokemonModel->getPokemon();
		$baseStats = $pokemonModel->getBaseStats();
		$versionGroup = $this->averagedPokemonModel->getVersionGroup();
		$generation = $this->averagedPokemonModel->getGeneration();

		// Get abilities.
		$abilitiesData = $this->averagedPokemonModel->getAbilities();
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
		$itemsData = $this->averagedPokemonModel->getItems();
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
		$movesData = $this->averagedPokemonModel->getMoves();
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
		$formatIdentifier = $format->getIdentifier();
		$breadcrumbs = [[
			'url' => '/stats',
			'text' => 'Stats',
		], [
			'text' => 'Formats',
		], [
			'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
			'text' => $format->getName(),
		], [
			'text' => $dexPokemon->getName(),
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Stats - ' . $startMonth['name'] . ' through '
					. $endMonth['name'] . ' ' . $format->getName() . ' - ' . $dexPokemon->getName(),
				'format' => [
					'identifier' => $format->getIdentifier(),
					'smogonDexIdentifier' => $format->getSmogonDexIdentifier(),
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->getIdentifier(),
					'name' => $dexPokemon->getName(),
					'image' => $pokemonModel->getImage(),
					'types' => $this->dexFormatter->formatDexTypes($dexPokemon->getTypes()),
					'baseStats' => $baseStats,
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'startMonth' => $startMonth,
				'endMonth' => $endMonth,
				'ratings' => $this->averagedPokemonModel->getRatings(),

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],
				'generation' => [
					'smogonDexIdentifier' => $generation->getSmogonDexIdentifier(),
				],

				// The main data.
				'showAbilities' => $versionGroup->getId()->hasAbilities(),
				'showItems' => $versionGroup->getId()->hasHeldItems(),
				'abilities' => $abilities,
				'items' => $items,
				'moves' => $moves,
			]
		]);
	}
}
