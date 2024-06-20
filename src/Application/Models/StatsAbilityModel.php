<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescription;
use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsAbilityPokemon;
use Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface;

final class StatsAbilityModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private string $abilityIdentifier;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private AbilityName $abilityName;
	private AbilityDescription $abilityDescription;

	/** @var StatsAbilityPokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private DateModel $dateModel,
		private FormatRepositoryInterface $formatRepository,
		private AbilityRepositoryInterface $abilityRepository,
		private RatingQueriesInterface $ratingQueries,
		private AbilityNameRepositoryInterface $abilityNameRepository,
		private AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		private StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository,
	) {}


	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $abilityIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->abilityIdentifier = $abilityIdentifier;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the ability.
		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId()
		);

		// Get the ability name.
		$this->abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->getId()
		);

		// Get the ability description.
		$this->abilityDescription = $this->abilityDescriptionRepository->getByAbility(
			$this->format->getGenerationId(),
			$languageId,
			$ability->getId()
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsAbilityPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$ability->getId(),
			$this->format->getGenerationId(),
			$languageId
		);
	}


	/**
	 * Get the month.
	 */
	public function getMonth() : string
	{
		return $this->month;
	}

	/**
	 * Get the format.
	 */
	public function getFormat() : Format
	{
		return $this->format;
	}

	/**
	 * Get the rating.
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the ability identifier.
	 */
	public function getAbilityIdentifier() : string
	{
		return $this->abilityIdentifier;
	}

	/**
	 * Get the language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
	 */
	public function getDateModel() : DateModel
	{
		return $this->dateModel;
	}

	/**
	 * Get the ratings for this month.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	/**
	 * Get the ability name.
	 */
	public function getAbilityName() : AbilityName
	{
		return $this->abilityName;
	}

	/**
	 * Get the ability description.
	 */
	public function getAbilityDescription() : AbilityDescription
	{
		return $this->abilityDescription;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return StatsAbilityPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
