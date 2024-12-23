<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsAbilityPokemon;
use Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatsAbilityModel
{
	private(set) string $month;
	private(set) Format $format;
	private(set) int $rating;
	private(set) array $ability;
	private(set) LanguageId $languageId;
	private(set) VersionGroup $versionGroup;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) string $speedName = '';

	/** @var StatsAbilityPokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly AbilityRepositoryInterface $abilityRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly AbilityNameRepositoryInterface $abilityNameRepository,
		private readonly AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsAbilityPokemonRepositoryInterface $statsAbilityPokemonRepository,
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
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		$this->versionGroup = $this->vgRepository->getById(
			$this->format->versionGroupId
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->id);
		$thisMonth = $this->dateModel->thisMonth;
		$prevMonth = $this->dateModel->prevMonth;

		// Get the ability.
		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->id,
		);

		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$ability->id,
		);
		$abilityDescription = $this->abilityDescriptionRepository->getByAbility(
			$this->format->versionGroupId,
			$languageId,
			$ability->id,
		);

		$this->ability = [
			'identifier' => $abilityIdentifier,
			'name' => $abilityName->name,
			'description' => $abilityDescription->description,
		];

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->getName();

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsAbilityPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->id,
			$rating,
			$ability->id,
			$languageId,
		);
	}
}
