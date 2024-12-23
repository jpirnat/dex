<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsMovePokemon;
use Jp\Dex\Domain\Usage\StatsMovePokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatsMoveModel
{
	private(set) string $month;
	private(set) Format $format;
	private(set) int $rating;
	private(set) array $move;
	private(set) LanguageId $languageId;
	private(set) VersionGroup $versionGroup;

	/** @var int[] $ratings */
	private(set) array $ratings = [];

	private(set) string $speedName = '';

	/** @var StatsMovePokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly MoveRepositoryInterface $moveRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly MoveNameRepositoryInterface $moveNameRepository,
		private readonly MoveDescriptionRepositoryInterface $moveDescriptionRepository,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsMovePokemonRepositoryInterface $statsMovePokemonRepository,
	) {}


	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $moveIdentifier,
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

		// Get the move.
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->id,
		);

		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$move->id,
		);
		$moveDescription = $this->moveDescriptionRepository->getByMove(
			$this->format->versionGroupId,
			$languageId,
			$move->id,
		);

		$this->move = [
			'identifier' => $moveIdentifier,
			'name' => $moveName->name,
			'description' => $moveDescription->description,
		];

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->getName();

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsMovePokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->id,
			$rating,
			$move->id,
			$languageId,
		);
	}
}
