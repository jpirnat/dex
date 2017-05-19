<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammate;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;

class MovesetPokemonMonthModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var MovesetPokemonRepositoryInterface $movesetPokemonRepository */
	private $movesetPokemonRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;

	/** @var MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository */
	private $movesetRatedAbilityRepository;

	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	private $movesetRatedItemRepository;

	/** @var MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository */
	private $movesetRatedSpreadRepository;

	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

	/** @var MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository */
	private $movesetRatedTeammateRepository;

	/** @var MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository */
	private $movesetRatedCounterRepository;


	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var string $pokemonIdentifier */
	private $pokemonIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;


	/** @var MovesetPokemon $movesetPokemon */
	private $movesetPokemon;

	/** @var MovesetRatedPokemon $movesetRatedPokemon */
	private $movesetRatedPokemon;

	/** @var MovesetRatedAbility[] $abilities */
	private $abilities = [];

	/** @var MovesetRatedItem[] $items */
	private $items = [];

	/** @var MovesetRatedSpread[] $spreads */
	private $spreads = [];

	/** @var MovesetRatedMove[] $moves */
	private $moves = [];

	/** @var MovesetRatedTeammate[] $teammates */
	private $teammates = [];

	/** @var MovesetRatedCounter[] $counters */
	private $counters = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param MovesetPokemonRepositoryInterface $movesetPokemonRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 * @param MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository
	 * @param MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		MovesetPokemonRepositoryInterface $movesetPokemonRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		MovesetRatedSpreadRepositoryInterface $movesetRatedSpreadRepository,
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->movesetPokemonRepository = $movesetPokemonRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
		$this->movesetRatedSpreadRepository = $movesetRatedSpreadRepository;
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->movesetRatedTeammateRepository = $movesetRatedTeammateRepository;
		$this->movesetRatedCounterRepository = $movesetRatedCounterRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $pokemonIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier,
		LanguageId $languageId
	) : void {
		$this->year = $year;
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->pokemonIdentifier = $pokemonIdentifier;
		$this->languageId = $languageId;

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get the Pokémon.
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		// Get the moveset Pokémon record.
		$this->movesetPokemon = $this->movesetPokemonRepository->getByYearAndMonthAndFormatAndPokemon(
			$year,
			$month,
			$format->getId(),
			$pokemon->getId()
		);

		// Get moveset rated Pokémon record.
		$this->movesetRatedPokemon = $this->movesetRatedPokemonRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated ability records.
		$this->abilities = $this->movesetRatedAbilityRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated item records.
		$this->items = $this->movesetRatedItemRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated spread records.
		$this->spreads = $this->movesetRatedSpreadRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated move records.
		$this->moves = $this->movesetRatedMoveRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated teammate records.
		$this->teammates = $this->movesetRatedTeammateRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);

		// Get moveset rated counter records.
		$this->counters = $this->movesetRatedCounterRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$format->getId(),
			$rating,
			$pokemon->getId()
		);
	}

	/**
	 * Get the year.
	 *
	 * @return int
	 */
	public function getYear() : int
	{
		return $this->year;
	}

	/**
	 * Get the month.
	 *
	 * @return int
	 */
	public function getMonth() : int
	{
		return $this->month;
	}

	/**
	 * Get the format identifier.
	 *
	 * @return string
	 */
	public function getFormatIdentifier() : string
	{
		return $this->formatIdentifier;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the Pokémon identifier.
	 *
	 * @return string
	 */
	public function getPokemonIdentifier() : string
	{
		return $this->pokemonIdentifier;
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the moveset Pokémon record.
	 *
	 * @return MovesetPokemon
	 */
	public function getMovesetPokemon() : MovesetPokemon
	{
		return $this->movesetPokemon;
	}

	/**
	 * Get the moveset rated Pokémon record.
	 *
	 * @return MovesetRatedPokemon
	 */
	public function getMovesetRatedPokemon() : MovesetRatedPokemon
	{
		return $this->movesetRatedPokemon;
	}

	/**
	 * Get the moveset rated ability records.
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}

	/**
	 * Get the moveset rated item records.
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	/**
	 * Get the moveset rated spread records.
	 *
	 * @return MovesetRatedSpread[]
	 */
	public function getSpreads() : array
	{
		return $this->spreads;
	}

	/**
	 * Get the moveset rated move records.
	 *
	 * @return MovesetRatedMove[]
	 */
	public function getMoves() : array
	{
		return $this->moves;
	}

	/**
	 * Get the moveset rated teammate records.
	 *
	 * @return MovesetRatedTeammate[]
	 */
	public function getTeammates() : array
	{
		return $this->teammates;
	}

	/**
	 * Get the moveset rated counter records.
	 *
	 * @return MovesetRatedCounter[]
	 */
	public function getCounters() : array
	{
		return $this->counters;
	}
}
