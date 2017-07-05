<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Application\Models\UsageMonth\UsageData;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\YearMonth;

class UsageMonthModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var DateHelper $dateHelper */
	private $dateHelper;

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

	/** @var UsageData[] $usageDatas */
	private $usageDatas = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param DateHelper $dateHelper
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		DateHelper $dateHelper,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->dateHelper = $dateHelper;
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

		// Calculate the previous month.
		$thisMonth = new YearMonth($year, $month);
		$lastMonth = $this->dateHelper->getPreviousMonth($thisMonth);

		// Get usage Pokémon records for this month.
		$usagePokemons = $this->usagePokemonRepository->getByYearAndMonthAndFormat(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId()
		);

		// Get usage Pokémon records for last month.
		$lastMonthUsages = $this->usagePokemonRepository->getByYearAndMonthAndFormat(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$format->getId()
		);

		// Get usage rated Pokémon records for this month.
		$usageRatedPokemons = $this->usageRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get usage rated Pokémon records for last month.
		$lastMonthRateds = $this->usageRatedPokemonRepository->getByYearAndMonthAndFormatAndRating(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get each usage record's data.
		foreach ($usageRatedPokemons as $usageRatedPokemon) {
			$pokemonId = $usageRatedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's non-rated usage record for this month.
			$usagePokemon = $usagePokemons[$pokemonId->value()];

			// Get this Pokémon's change in usage percent since last month.
			$lastMonthUsagePercent = 0;
			if (isset($lastMonthRateds[$pokemonId->value()])) {
				$lastMonthUsagePercent = $lastMonthRateds[$pokemonId->value()]->getUsagePercent();
			}
			$usageChange = $usageRatedPokemon->getUsagePercent() - $lastMonthUsagePercent;

			// Get this Pokémon's change in raw percent and real percent since last month.
			$lastMonthRawPercent = 0;
			$lastMonthRealPercent = 0;
			if (isset($lastMonthUsages[$pokemonId->value()])) {
				$lastMonthRawPercent = $lastMonthUsages[$pokemonId->value()]->getRawPercent();
				$lastMonthRealPercent = $lastMonthUsages[$pokemonId->value()]->getRealPercent();
			}
			$rawChange = $usagePokemon->getRawPercent() - $lastMonthRawPercent;
			$realChange = $usagePokemon->getRealPercent() - $lastMonthRealPercent;

			$this->usageDatas[] = new UsageData(
				$usageRatedPokemon->getRank(),
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$usageRatedPokemon->getUsagePercent(),
				$usageChange,
				$usagePokemon->getRaw(),
				$usagePokemon->getRawPercent(),
				$rawChange,
				$usagePokemon->getReal(),
				$usagePokemon->getRealPercent(),
				$realChange
			);
		}
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
	 * Get the usage datas.
	 *
	 * @return UsageData[]
	 */
	public function getUsageDatas() : array
	{
		return $this->usageDatas;
	}
}
