<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesetPokemonMonth\AbilityData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\CounterData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\ItemData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\MoveData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\SpreadData;
use Jp\Dex\Application\Models\MovesetPokemonMonth\TeammateData;
use Jp\Dex\Application\Models\MovesetPokemonMonthModel;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MovesetPokemonMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var MovesetPokemonMonthModel $movesetPokemonMonthModel */
	private $movesetPokemonMonthModel;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;


	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param MovesetPokemonMonthModel $movesetPokemonMonthModel
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 */
	public function __construct(
		Twig_Environment $twig,
		MovesetPokemonMonthModel $movesetPokemonMonthModel,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository
	) {
		$this->twig = $twig;
		$this->movesetPokemonMonthModel = $movesetPokemonMonthModel;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
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
		$languageId = $this->movesetPokemonMonthModel->getLanguageId();

		$movesetPokemon = $this->movesetPokemonMonthModel->getMovesetPokemon();
		$movesetRatedPokemon = $this->movesetPokemonMonthModel->getMovesetRatedPokemon();

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$movesetPokemon->getPokemonId()
		);

		// Get abilities and sort by percent.
		$abilityDatas = $this->movesetPokemonMonthModel->getAbilityDatas();
		uasort(
			$abilityDatas,
			function (AbilityData $a, AbilityData $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all ability data into the right form.
		$abilities = [];
		foreach ($abilityDatas as $abilityData) {
			$abilities[] = [
				'name' => $abilityData->getAbilityName(),
				'percent' => $abilityData->getPercent(),
				'change' => $abilityData->getChange(),
			];
		}

		// Get items and sort by percent.
		$itemDatas = $this->movesetPokemonMonthModel->getItemDatas();
		uasort(
			$itemDatas,
			function (ItemData $a, ItemData $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all item data into the right form.
		$items = [];
		foreach ($itemDatas as $itemData) {
			$items[] = [
				'name' => $itemData->getItemName(),
				'percent' => $itemData->getPercent(),
				'change' => $itemData->getChange(),
			];
		}

		// Get spreads and sort by percent.
		$spreadDatas = $this->movesetPokemonMonthModel->getSpreadDatas();
		uasort(
			$spreadDatas,
			function (SpreadData $a, SpreadData $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all spread data into the right form..
		$spreads = [];
		$statIds = [
			'hp' =>  new StatId(StatId::HP),
			'atk' => new StatId(StatId::ATTACK),
			'def' => new StatId(StatId::DEFENSE),
			'spa' => new StatId(StatId::SPECIAL_ATTACK),
			'spd' => new StatId(StatId::SPECIAL_DEFENSE),
			'spe' => new StatId(StatId::SPEED),
		];
		foreach ($spreadDatas as $spreadData) {
			// Create nature array with nature name and stat modifiers.
			$natureModifiers = $spreadData->getNatureModifiers();
			$nature['name'] = $spreadData->getNatureName();
			foreach ($statIds as $statAbbr => $statId) {
				// No natures modify HP.
				if ($statId->value() === StatId::HP) {
					continue;
				}

				$natureModifier = $natureModifiers->get($statId)->getValue();
				if ($natureModifier > 1) {
					$natureModifierSign = '+';
				} elseif ($natureModifier < 1) {
					$natureModifierSign = '-';
				} else {
					$natureModifierSign = '';
				}

				$nature[$statAbbr] = $natureModifierSign;
			}

			// Create EVs array with each stat's EV.
			$evSpread = $spreadData->getEvSpread();
			$evs = [];
			foreach ($statIds as $statAbbr => $statId) {
				$evs[$statAbbr] = $evSpread->get($statId)->getValue();
			}

			// Create stats array with each calculated stat.
			$statSpread = $spreadData->getStatSpread();
			$stats = [];
			foreach ($statIds as $statAbbr => $statId) {
				$stats[$statAbbr] = $statSpread->get($statId)->getValue();
			}

			// Put it all together!
			$spreads[] = [
				'nature' => $nature,
				'evs' => $evs,
				'percent' => $spreadData->getPercent(),
				'stats' => $stats,
			];
		}

		// Get moves and sort by percent.
		$moveDatas = $this->movesetPokemonMonthModel->getMoveDatas();
		uasort(
			$moveDatas,
			function (MoveData $a, MoveData $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Compile all move data into the right form.
		$moves = [];
		foreach ($moveDatas as $moveData) {
			$moves[] = [
				'name' => $moveData->getMoveName(),
				'percent' => $moveData->getPercent(),
				'change' => $moveData->getChange(),
			];
		}

		// Get teammates and sort by percent.
		$teammateDatas = $this->movesetPokemonMonthModel->getTeammateDatas();
		uasort(
			$teammateDatas,
			function (TeammateData $a, TeammateData $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get teammate names.
		$teammates = [];
		foreach ($teammateDatas as $teammateData) {
			$teammates[] = [
				'name' => $teammateData->getPokemonName(),
				'identifier' => $teammateData->getPokemonIdentifier(),
				'percent' => $teammateData->getPercent(),
			];
		}

		// Get counters and sort by percent.
		$counterDatas = $this->movesetPokemonMonthModel->getCounterDatas();
		uasort(
			$counterDatas,
			function (CounterData $a, CounterData $b) {
				return $b->getNumber1() <=> $a->getNumber1();
			}
		);

		// Compile all counter data into the right form.
		$counters = [];
		foreach ($counterDatas as $counterData) {
			$counters[] = [
				'name' => $counterData->getPokemonName(),
				'identifier' => $counterData->getPokemonIdentifier(),
				'number1' => $counterData->getNumber1(),
				'number2' => $counterData->getNumber2(),
				'number3' => $counterData->getNumber3(),
				'percentKnockedOut' => $counterData->getPercentKnockedOut(),
				'percentSwitchedOut' => $counterData->getPercentSwitchedOut(),
			];
		}

		$content = $this->twig->render(
			'moveset-pokemon-month.twig',
			[
				'year' => $this->movesetPokemonMonthModel->getYear(),
				'month' => $this->movesetPokemonMonthModel->getMonth(),
				'formatIdentifier' => $this->movesetPokemonMonthModel->getFormatIdentifier(),
				'rating' => $this->movesetPokemonMonthModel->getRating(),
				'pokemonName' => $pokemonName->getName(),
				'rawCount' =>$movesetPokemon->getRawCount(),
				'averageWeight' => $movesetRatedPokemon->getAverageWeight(),
				'viabilityCeiling' => $movesetPokemon->getViabilityCeiling(),
				'abilities' => $abilities,
				'items' => $items,
				'spreads' => $spreads,
				'moves' => $moves,
				'teammates' => $teammates,
				'counters' => $counters,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
