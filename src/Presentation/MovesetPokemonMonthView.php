<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesetPokemonMonthModel;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MovesetPokemonMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var MovesetPokemonMonthModel $movesetPokemonMonthModel */
	private $movesetPokemonMonthModel;


	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var NatureNameRepositoryInterface $natureNameRepository */
	private $natureNameRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param MovesetPokemonMonthModel $movesetPokemonMonthModel
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param NatureNameRepositoryInterface $natureNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 */
	public function __construct(
		Twig_Environment $twig,
		MovesetPokemonMonthModel $movesetPokemonMonthModel,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		NatureNameRepositoryInterface $natureNameRepository,
		MoveNameRepositoryInterface $moveNameRepository
	) {
		$this->twig = $twig;
		$this->movesetPokemonMonthModel = $movesetPokemonMonthModel;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->natureNameRepository = $natureNameRepository;
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

		$movesetRatedAbilities = $this->movesetPokemonMonthModel->getMovesetRatedAbilities();
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

/*
		$movesetRatedItems = $this->movesetPokemonMonthModel->getMovesetRatedItems();
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
*/

		$movesetRatedMoves = $this->movesetPokemonMonthModel->getMovesetRatedMoves();
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

		$content = $this->twig->render(
			'moveset-pokemon-month.twig',
			[
				'rawCount' =>$movesetPokemon->getRawCount(),
				'averageWeight' => $movesetRatedPokemon->getAverageWeight(),
				'viabilityCeiling' => $movesetPokemon->getViabilityCeiling(),
				'abilities' => $abilities,
				// 'items' => $items,
				'moves' => $moves,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
// TODO: sort each section by percent.
// TODO: enable items, once the name data is added.
