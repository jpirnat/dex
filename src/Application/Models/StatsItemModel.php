<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatsItemModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private array $item;
	private LanguageId $languageId;
	private VersionGroup $versionGroup;

	/** @var int[] $ratings */
	private array $ratings = [];

	private string $speedName = '';

	/** @var StatsItemPokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly ItemRepositoryInterface $itemRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly DexItemRepositoryInterface $dexItemRepository,
		private readonly StatNameRepositoryInterface $statNameRepository,
		private readonly StatsItemPokemonRepositoryInterface $statsItemPokemonRepository,
	) {}


	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $itemIdentifier,
		LanguageId $languageId,
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->item = [];
		$this->languageId = $languageId;

		// Get the format.
		$this->format = $this->formatRepository->getByIdentifier(
			$formatIdentifier,
			$languageId,
		);

		$this->versionGroup = $this->vgRepository->getById(
			$this->format->getVersionGroupId()
		);

		// Get the previous month and the next month.
		$this->dateModel->setMonthAndFormat($month, $this->format->getId());
		$thisMonth = $this->dateModel->getThisMonth();
		$prevMonth = $this->dateModel->getPrevMonth();

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId(),
		);

		$dexItem = $this->dexItemRepository->getById(
			$this->format->getVersionGroupId(),
			$item->getId(),
			$languageId,
		);
		$this->item = [
			'icon' => $dexItem->getIcon(),
			'identifier' => $dexItem->getIdentifier(),
			'name' => $dexItem->getName(),
			'description' => $dexItem->getDescription(),
		];

		$speedName = $this->statNameRepository->getByLanguageAndStat(
			$languageId,
			new StatId(StatId::SPEED),
		);
		$this->speedName = $speedName->getName();

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsItemPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$item->getId(),
			$languageId,
		);
	}


	public function getMonth() : string
	{
		return $this->month;
	}

	public function getFormat() : Format
	{
		return $this->format;
	}

	public function getRating() : int
	{
		return $this->rating;
	}

	public function getItem() : array
	{
		return $this->item;
	}

	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	public function getDateModel() : DateModel
	{
		return $this->dateModel;
	}

	public function getVersionGroup() : VersionGroup
	{
		return $this->versionGroup;
	}

	/**
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}

	public function getSpeedName() : string
	{
		return $this->speedName;
	}

	/**
	 * @return StatsItemPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
