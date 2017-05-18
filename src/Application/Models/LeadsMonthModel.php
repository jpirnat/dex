<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;

class LeadsMonthModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var LeadsPokemonRepositoryInterface $leadsPokemonRepository */
	private $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

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

	/** @var LeadsPokemon[] $leadsPokemon */
	private $leadsPokemon = [];

	/** @var LeadsRatedPokemon[] $leadsRatedPokemon */
	private $leadsRatedPokemon = [];

	/** @var Pokemon[] $pokemon */
	private $pokemon = [];

	/** @var PokemonName[] $pokemonNames */
	private $pokemonNames = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param LeadsPokemonRepositoryInterface $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		LeadsPokemonRepositoryInterface $leadsPokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->leadsPokemonRepository = $leadsPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
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

		// Get leads Pokémon records.
		$this->leadsPokemon = $this->leadsPokemonRepository->getByYearAndMonthAndFormat(
			$year,
			$month,
			$format->getId()
		);

		// Get leads rated Pokémon records.
		$this->leadsRatedPokemon = $this->leadsRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
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
	 * Get the leads Pokémon records.
	 *
	 * @return LeadsPokemon[]
	 */
	public function getLeadsPokemon() : array
	{
		return $this->leadsPokemon;
	}

	/**
	 * Get the leads rated Pokémon records.
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getLeadsRatedPokemon() : array
	{
		return $this->leadsRatedPokemon;
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
