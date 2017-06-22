<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesetPokemonMonthModel;
use Jp\Dex\Application\Models\MovesetPokemonMonthSpreadModel;
use Jp\Dex\Application\Models\SpreadData;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammate;
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

	/** @var MovesetPokemonMonthSpreadModel $movesetPokemonMonthSpreadModel */
	private $movesetPokemonMonthSpreadModel;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;


	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param MovesetPokemonMonthModel $movesetPokemonMonthModel
	 * @param MovesetPokemonMonthSpreadModel $movesetPokemonMonthSpreadModel
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 */
	public function __construct(
		Twig_Environment $twig,
		MovesetPokemonMonthModel $movesetPokemonMonthModel,
		MovesetPokemonMonthSpreadModel $movesetPokemonMonthSpreadModel,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		MoveNameRepositoryInterface $moveNameRepository
	) {
		$this->twig = $twig;
		$this->movesetPokemonMonthModel = $movesetPokemonMonthModel;
		$this->movesetPokemonMonthSpreadModel = $movesetPokemonMonthSpreadModel;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->moveNameRepository = $moveNameRepository;
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
		$movesetRatedAbilities = $this->movesetPokemonMonthModel->getAbilities();
		uasort(
			$movesetRatedAbilities,
			function (MovesetRatedAbility $a, MovesetRatedAbility $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get ability names.
		$abilities = [];
		foreach ($movesetRatedAbilities as $movesetRatedAbility) {
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$movesetRatedAbility->getAbilityId()
			);

			$abilities[] = [
				'name' => $abilityName->getName(),
				'percent' => $movesetRatedAbility->getPercent(),
			];
		}

		// Get items and sort by percent.
		$movesetRatedItems = $this->movesetPokemonMonthModel->getItems();
		uasort(
			$movesetRatedItems,
			function (MovesetRatedItem $a, MovesetRatedItem $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get item names.
		$items = [];
		foreach ($movesetRatedItems as $movesetRatedItem) {
			$itemName = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$movesetRatedItem->getItemId()
			);

			$items[] = [
				'name' => $itemName->getName(),
				'percent' => $movesetRatedItem->getPercent(),
			];
		}

		// Get spreads and sort by percent.
		$spreadDatas = $this->movesetPokemonMonthSpreadModel->getSpreadDatas();
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
		$movesetRatedMoves = $this->movesetPokemonMonthModel->getMoves();
		uasort(
			$movesetRatedMoves,
			function (MovesetRatedMove $a, MovesetRatedMove $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get move names.
		$moves = [];
		foreach ($movesetRatedMoves as $movesetRatedMove) {
			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$movesetRatedMove->getMoveId()
			);

			$moves[] = [
				'name' => $moveName->getName(),
				'percent' => $movesetRatedMove->getPercent(),
			];
		}

		// Get teammates and sort by percent.
		$movesetRatedTeammates = $this->movesetPokemonMonthModel->getTeammates();
		uasort(
			$movesetRatedTeammates,
			function (MovesetRatedTeammate $a, MovesetRatedTeammate $b) {
				return $b->getPercent() <=> $a->getPercent();
			}
		);

		// Get teammate names.
		$teammates = [];
		foreach ($movesetRatedTeammates as $movesetRatedTeammate) {
			$teammateName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$movesetRatedTeammate->getTeammateId()
			);

			$teammatePokemon = $this->pokemonRepository->getById(
				$movesetRatedTeammate->getTeammateId()
			);

			$teammates[] = [
				'identifier' => $teammatePokemon->getIdentifier(),
				'name' => $teammateName->getName(),
				'percent' => $movesetRatedTeammate->getPercent(),
			];
		}

		// Get counters and sort by percent.
		$movesetRatedCounters = $this->movesetPokemonMonthModel->getCounters();
		uasort(
			$movesetRatedCounters,
			function (MovesetRatedCounter $a, MovesetRatedCounter $b) {
				return $b->getNumber1() <=> $a->getNumber1();
			}
		);

		// Get counter names.
		$counters = [];
		foreach ($movesetRatedCounters as $movesetRatedCounter) {
			$counterName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$movesetRatedCounter->getCounterId()
			);

			$counterPokemon = $this->pokemonRepository->getById(
				$movesetRatedCounter->getCounterId()
			);

			$counters[] = [
				'identifier' => $counterPokemon->getIdentifier(),
				'name' => $counterName->getName(),
				'number1' => $movesetRatedCounter->getNumber1(),
				'number2' => $movesetRatedCounter->getNumber2(),
				'number3' => $movesetRatedCounter->getNumber3(),
				'percentKnockedOut' => $movesetRatedCounter->getPercentKnockedOut(),
				'percentSwitchedOut' => $movesetRatedCounter->getPercentSwitchedOut(),
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
