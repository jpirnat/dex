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
		$response = new Response();
		$response->getBody()->write('');
		return $response;
	}
}


