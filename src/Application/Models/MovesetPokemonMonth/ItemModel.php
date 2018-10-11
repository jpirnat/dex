<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;

class ItemModel
{
	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	private $movesetRatedItemRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var ItemData[] $itemDatas */
	private $itemDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param ItemRepositoryInterface $itemRepository
	 */
	public function __construct(
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		ItemRepositoryInterface $itemRepository
	) {
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->itemRepository = $itemRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param DateTime $thisMonth
	 * @param DateTime $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $thisMonth,
		DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated item records for this month.
		$movesetRatedItems = $this->movesetRatedItemRepository->getByMonthAndFormatAndRatingAndPokemon(
			$thisMonth,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get moveset rated item records for the previous month.
		$prevMonthItems = $this->movesetRatedItemRepository->getByMonthAndFormatAndRatingAndPokemon(
			$prevMonth,
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

			// Get this item.
			$item = $this->itemRepository->getById($itemId);

			// Get this item's percent from the previous month.
			if (isset($prevMonthItems[$itemId->value()])) {
				$change = $movesetRatedItem->getPercent() - $prevMonthItems[$itemId->value()]->getPercent();
			} else {
				$change = $movesetRatedItem->getPercent();
			}

			$this->itemDatas[] = new ItemData(
				$itemName->getName(),
				$item->getIdentifier(),
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
