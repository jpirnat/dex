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
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;

final class StatsItemModel
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var RatingQueriesInterface $ratingQueries */
	private $ratingQueries;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var ItemDescriptionRepositoryInterface $itemDescriptionRepository */
	private $itemDescriptionRepository;

	/** @var StatsItemPokemonRepositoryInterface $statsItemPokemonRepository */
	private $statsItemPokemonRepository;


	/** @var string $month */
	private $month;

	/** @var Format $format */
	private $format;

	/** @var int $rating */
	private $rating;

	/** @var string $itemIdentifier */
	private $itemIdentifier;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var int[] $ratings */
	private $ratings = [];

	/** @var ItemName $itemName */
	private $itemName;

	/** @var ItemDescription $itemDescription */
	private $itemDescription;

	/** @var StatsItemPokemon[] $pokemon */
	private $pokemon = [];


	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param FormatRepositoryInterface $formatRepository
	 * @param ItemRepositoryInterface $itemRepository
	 * @param RatingQueriesInterface $ratingQueries
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param ItemDescriptionRepositoryInterface $itemDescriptionRepository
	 * @param StatsItemPokemonRepositoryInterface $statsItemPokemonRepository
	 */
	public function __construct(
		DateModel $dateModel,
		FormatRepositoryInterface $formatRepository,
		ItemRepositoryInterface $itemRepository,
		RatingQueriesInterface $ratingQueries,
		ItemNameRepositoryInterface $itemNameRepository,
		ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		StatsItemPokemonRepositoryInterface $statsItemPokemonRepository
	) {
		$this->dateModel = $dateModel;
		$this->formatRepository = $formatRepository;
		$this->itemRepository = $itemRepository;
		$this->ratingQueries = $ratingQueries;
		$this->itemNameRepository = $itemNameRepository;
		$this->itemDescriptionRepository = $itemDescriptionRepository;
		$this->statsItemPokemonRepository = $statsItemPokemonRepository;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param string $month
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $itemIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $month,
		string $formatIdentifier,
		int $rating,
		string $itemIdentifier,
		LanguageId $languageId
	) : void {
		$this->month = $month;
		$this->rating = $rating;
		$this->itemIdentifier = $itemIdentifier;
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

		// Get the item.
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		// Get the ratings for this month.
		$this->ratings = $this->ratingQueries->getByMonthAndFormat(
			$thisMonth,
			$this->format->getId()
		);

		// Get the item name.
		$this->itemName = $this->itemNameRepository->getByLanguageAndItem(
			$languageId,
			$item->getId()
		);

		// Get the item description.
		$this->itemDescription = $this->itemDescriptionRepository->getByGenerationAndLanguageAndItem(
			$this->format->getGenerationId(),
			$languageId,
			$item->getId()
		);

		// Get the Pokémon usage data.
		$this->pokemon = $this->statsItemPokemonRepository->getByMonth(
			$thisMonth,
			$prevMonth,
			$this->format->getId(),
			$rating,
			$item->getId(),
			$this->format->getGenerationId(),
			$languageId
		);
	}

	/**
	 * Get the month.
	 *
	 * @return string
	 */
	public function getMonth() : string
	{
		return $this->month;
	}

	/**
	 * Get the format.
	 *
	 * @return Format
	 */
	public function getFormat() : Format
	{
		return $this->format;
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
	 * Get the item identifier.
	 *
	 * @return string
	 */
	public function getItemIdentifier() : string
	{
		return $this->itemIdentifier;
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the date model.
	 *
	 * @return DateModel
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
	 *
	 * @return ItemName
	 */
	public function getItemName() : ItemName
	{
		return $this->itemName;
	}

	/**
	 * Get the item description.
	 *
	 * @return ItemDescription
	 */
	public function getItemDescription() : ItemDescription
	{
		return $this->itemDescription;
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
