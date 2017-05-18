<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
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

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var UsagePokemon[] $usagePokemon */
	private $usagePokemon = [];

	/** @var UsageRatedPokemon[] $usageRatedPokemon */
	private $usageRatedPokemon = [];

	/** @var PokemonName[] $pokemonNames */
	private $pokemonNames = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
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

		// Get Pokémon names.
		$this->pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);
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
	 * Get the Pokémon names.
	 *
	 * @return PokemonName[]
	 */
	public function getPokemonNames() : array
	{
		return $this->pokemonNames;
	}
}
