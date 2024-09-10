<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveDescription;
use Jp\Dex\Domain\Moves\MoveDescriptionRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveName;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsMovePokemon;
use Jp\Dex\Domain\Usage\StatsMovePokemonRepositoryInterface;

final class StatsMoveModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private string $moveIdentifier;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private MoveName $moveName;
	private MoveDescription $moveDescription;

	/** @var StatsMovePokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly MoveRepositoryInterface $moveRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly MoveNameRepositoryInterface $moveNameRepository,
		private readonly MoveDescriptionRepositoryInterface $moveDescriptionRepository,
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
		$this->moveIdentifier = $moveIdentifier;
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the move.
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId(),
		);

		// Get the move name.
		$this->moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$move->getId(),
		);

		// Get the move description.
		$this->moveDescription = $this->moveDescriptionRepository->getByMove(
			$this->format->getVersionGroupId(),
			$languageId,
			$move->getId(),
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsMovePokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$move->getId(),
			$this->format->getVersionGroupId(),
			$languageId,
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
	 * Get the move identifier.
	 */
	public function getMoveIdentifier() : string
	{
		return $this->moveIdentifier;
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
	 * Get the move name.
	 */
	public function getMoveName() : MoveName
	{
		return $this->moveName;
	}

	/**
	 * Get the move description.
	 */
	public function getMoveDescription() : MoveDescription
	{
		return $this->moveDescription;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return StatsMovePokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
