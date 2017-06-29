<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use Jp\Dex\Domain\YearMonth;

class ItemModel
{
	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	private $movesetRatedItemRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var ItemData[] $itemDatas */
	private $itemDatas;

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 */
	public function __construct(
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		ItemNameRepositoryInterface $itemNameRepository
	) {
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
		$this->itemNameRepository = $itemNameRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param YearMonth $thisMonth
	 * @param YearMonth $lastMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		YearMonth $thisMonth,
		YearMonth $lastMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated item records for this month.
		$movesetRatedItems = $this->movesetRatedItemRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get moveset rated item records for last month.
		$lastMonthItems = $this->movesetRatedItemRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each item's data.
		foreach ($movesetRatedItems as $movesetRatedItem) {
			$itemId = $movesetRatedItem->getItemId();

			// Get this item's name.
			$itemName = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$itemId
			);

			// Get this item's percent from last month.
			if (isset($lastMonthItems[$itemId->value()])) {
				$change = $movesetRatedItem->getPercent() - $lastMonthItems[$itemId->value()]->getPercent();
			} else {
				$change = $movesetRatedItem->getPercent();
			}

			$this->itemDatas[] = new ItemData(
				$itemName->getName(),
				$movesetRatedItem->getPercent(),
				$change
			);
		}
	}

	/**
	 * Get the item datas.
	 *
	 * @return ItemData[]
	 */
	public function getItemDatas() : array
	{
		return $this->itemDatas;
	}
}
