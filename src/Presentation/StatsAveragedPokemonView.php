<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatsAveragedPokemon\AbilityData;
use Jp\Dex\Application\Models\StatsAveragedPokemon\ItemData;
use Jp\Dex\Application\Models\StatsAveragedPokemon\MoveData;
use Jp\Dex\Application\Models\StatsAveragedPokemon\StatsAveragedPokemonModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class StatsAveragedPokemonView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsAveragedPokemonModel $statsAveragedPokemonModel */
	private $statsAveragedPokemonModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsAveragedPokemonModel $statsAveragedPokemonModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsAveragedPokemonModel $statsAveragedPokemonModel,
		IntlFormatterFactory $formatterFactory,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->statsAveragedPokemonModel = $statsAveragedPokemonModel;
		$this->formatterFactory = $formatterFactory;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->statsAveragedPokemonModel->getStart();
		$end = $this->statsAveragedPokemonModel->getEnd();
		$format = $this->statsAveragedPokemonModel->getFormat();
		$rating = $this->statsAveragedPokemonModel->getRating();
		$pokemon = $this->statsAveragedPokemonModel->getPokemon();

		$formatter = $this->formatterFactory->createFor(
			$this->statsAveragedPokemonModel->getLanguageId()
		);

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->statsAveragedPokemonModel->getPokemonModel();
		$dexPokemon = $pokemonModel->getPokemon();
		$model = $pokemonModel->getModel();
		$generation = $this->statsAveragedPokemonModel->getGeneration();

		// Get base stats.
		$baseStats = [];
		foreach ($pokemonModel->getStatDatas() as $statData) {
			$baseStats[] = [
				'name' => $statData->getStatName(),
				'value' => $statData->getBaseStat(),
			];
		}

		// Get abilities and sort by percent.
		$abilityDatas = $this->statsAveragedPokemonModel->getAbilityDatas();
		uasort(
			$abilityDatas,
			function (AbilityData $a, AbilityData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all ability data into the right form.
		$abilities = [];
		foreach ($abilityDatas as $abilityData) {
			$abilities[] = [
				'name' => $abilityData->getAbilityName(),
				'identifier' => $abilityData->getAbilityIdentifier(),
				'percent' => $formatter->formatPercent($abilityData->getPercent()),
			];
		}

		// Get items and sort by percent.
		$itemDatas = $this->statsAveragedPokemonModel->getItemDatas();
		uasort(
			$itemDatas,
			function (ItemData $a, ItemData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all item data into the right form.
		$items = [];
		foreach ($itemDatas as $itemData) {
			$items[] = [
				'name' => $itemData->getItemName(),
				'identifier' => $itemData->getItemIdentifier(),
				'percent' => $formatter->formatPercent($itemData->getPercent()),
			];
		}

		// Get moves and sort by percent.
		$moveDatas = $this->statsAveragedPokemonModel->getMoveDatas();
		uasort(
			$moveDatas,
			function (MoveData $a, MoveData $b) : int {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all move data into the right form.
		$moves = [];
		foreach ($moveDatas as $moveData) {
			$moves[] = [
				'name' => $moveData->getMoveName(),
				'identifier' => $moveData->getMoveIdentifier(),
				'percent' => $formatter->formatPercent($moveData->getPercent()),
			];
		}

		// Navigation breadcrumbs.
		$formatIdentifier = $format->getIdentifier();
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => $dexPokemon->getName(),
			],
		];

		$content = $this->renderer->render(
			'html/stats/averaged-pokemon.twig',
			$this->baseView->getBaseVariables() + [
				'start' => $start,
				'end' => $end,
				'format' => [
					'identifier' => $format->getIdentifier(),
					'smogonDexIdentifier' => $format->getSmogonDexIdentifier(),
				],
				'rating' => $rating,
				'pokemon' => [
					'identifier' => $dexPokemon->getIdentifier(),
					'name' => $dexPokemon->getName(),
					'smogonDexIdentifier' => $pokemon->getSmogonDexIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,

				'ratings' => $this->statsAveragedPokemonModel->getRatings(),

				'model' => $model->getImage(),
				'types' => $this->dexFormatter->formatDexTypes($dexPokemon->getTypes()),
				'baseStats' => $baseStats,
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],

				// The main data.
				'showAbilities' => $generation->getId()->value() >= 3,
				'showItems' => $generation->getId()->value() >= 2,
				'abilities' => $abilities,
				'items' => $items,
				'moves' => $moves,
			]
		);

		return new HtmlResponse($content);
	}
}
