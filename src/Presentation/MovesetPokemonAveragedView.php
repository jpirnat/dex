<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesetPokemonAveraged\AbilityData;
use Jp\Dex\Application\Models\MovesetPokemonAveraged\ItemData;
use Jp\Dex\Application\Models\MovesetPokemonAveraged\MoveData;
use Jp\Dex\Application\Models\MovesetPokemonAveraged\MovesetPokemonAveragedModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class MovesetPokemonAveragedView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var MovesetPokemonAveragedModel $movesetPokemonAveragedModel */
	private $movesetPokemonAveragedModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param MovesetPokemonAveragedModel $movesetPokemonAveragedModel
	 * @param IntlFormatterFactory $formatterFactory
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		MovesetPokemonAveragedModel $movesetPokemonAveragedModel,
		IntlFormatterFactory $formatterFactory,
		DexFormatter $dexFormatter
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->movesetPokemonAveragedModel = $movesetPokemonAveragedModel;
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
		$start = $this->movesetPokemonAveragedModel->getStart();
		$end = $this->movesetPokemonAveragedModel->getEnd();
		$formatIdentifier = $this->movesetPokemonAveragedModel->getFormatIdentifier();
		$rating = $this->movesetPokemonAveragedModel->getRating();
		$pokemonIdentifier = $this->movesetPokemonAveragedModel->getPokemonIdentifier();

		$formatter = $this->formatterFactory->createFor(
			$this->movesetPokemonAveragedModel->getLanguageId()
		);

		// Get miscellaneous Pokémon data.
		$pokemonModel = $this->movesetPokemonAveragedModel->getPokemonModel();
		$pokemonName = $pokemonModel->getPokemonName();
		$model = $pokemonModel->getModel();

		// Get base stats.
		$baseStats = [];
		foreach ($pokemonModel->getStatDatas() as $statData) {
			$baseStats[] = [
				'name' => $statData->getStatName(),
				'value' => $statData->getBaseStat(),
			];
		}

		// Get abilities and sort by percent.
		$abilityDatas = $this->movesetPokemonAveragedModel->getAbilityDatas();
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
		$itemDatas = $this->movesetPokemonAveragedModel->getItemDatas();
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
		$moveDatas = $this->movesetPokemonAveragedModel->getMoveDatas();
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
				'text' => $pokemonName->getName(),
			],
		];

		$content = $this->twig->render(
			'html/moveset-pokemon-averaged.twig',
			$this->baseView->getBaseVariables() + [
				'breadcrumbs' => $breadcrumbs,

				'start' => $start,
				'end' => $end,
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'pokemonIdentifier' => $pokemonIdentifier,

				'pokemonName' => $pokemonName->getName(),
				'model' => $model->getImage(),
				'types' => $this->dexFormatter->formatDexPokemonTypes($pokemonModel->getTypes()),
				'baseStats' => $baseStats,

				// The main data.
				'abilities' => $abilities,
				'items' => $items,
				'moves' => $moves,
			]
		);

		return new HtmlResponse($content);
	}
}
