<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemon;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;

class UsageMonthModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var UsagePokemonRepositoryInterface $usagePokemonRepository */
	private $usagePokemonRepository;

	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	private $usageRatedPokemonRepository;

	/** PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var int $year */
	private $year;

	/** @var int $month */
	private $month;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var UsagePokemon[] $usagePokemon */
	private $usagePokemon = [];

	/** @var UsageRatedPokemon[] $usageRatedPokemon */
	private $usageRatedPokemon = [];

	/** @var Pokemon[] $pokemon */
	private $pokemon;

	/** @var PokemonName[] $pokemonNames */
	private $pokemonNames = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->year = $year;
		$this->month = $month;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get usage Pokémon records.
		$this->usagePokemon = $this->usagePokemonRepository->getByYearAndMonthAndFormat(
			$year,
			$month,
			$format->getId()
		);

		// Get usage rated Pokémon records.
		$this->usageRatedPokemon = $this->usageRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$year,
			$month,
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$this->pokemon = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$this->pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);
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
	 * Get the usage Pokémon records.
	 *
	 * @return UsagePokemon[]
	 */
	public function getUsagePokemon() : array
	{
		return $this->usagePokemon;
	}

	/**
	 * Get the usage rated Pokémon records.
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getUsageRatedPokemon() : array
	{
		return $this->usageRatedPokemon;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return Pokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the Pokémon names.
	 *
	 * @return PokemonName[]
	 */
	public function getPokemonNames() : array
	{
		return $this->pokemonNames;
	}
}
