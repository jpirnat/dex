<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemDescription;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;

final class StatsItemModel
{
	private string $month;
	private Format $format;
	private int $rating;
	private string $itemIdentifier;
	private LanguageId $languageId;

	/** @var int[] $ratings */
	private array $ratings = [];

	private ItemName $itemName;
	private ItemDescription $itemDescription;
	private string $speedName = '';

	/** @var StatsItemPokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private readonly DateModel $dateModel,
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly ItemRepositoryInterface $itemRepository,
		private readonly RatingQueriesInterface $ratingQueries,
		private readonly ItemNameRepositoryInterface $itemNameRepository,
		private readonly ItemDescriptionRepositoryInterface $itemDescriptionRepository,
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
		$this->itemIdentifier = $itemIdentifier;
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

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId(),
		);

		// Get the item name.
		$this->itemName = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			$item->getId(),
		);

		// Get the item description.
		$this->itemDescription = $this->itemDescriptionRepository->getByItem(
			$this->format->getVersionGroupId(),
			$languageId,
			$item->getId(),
		);

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
	 * Get the item identifier.
	 */
	public function getItemIdentifier() : string
	{
		return $this->itemIdentifier;
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
	 * Get the item name.
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
	}

	/**
	 * Get the item description.
	 */
	public function getItemDescription() : ItemDescription
	{
		return $this->itemDescription;
	}

	public function getSpeedName() : string
	{
		return $this->speedName;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return StatsItemPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
