<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;

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
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated item records.
		$movesetRatedItems = $this->movesetRatedItemRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each item's data.
		foreach ($movesetRatedItems as $movesetRatedItem) {
			// Get this item's name.
			$itemName = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$movesetRatedItem->getItemId()
			);

			$this->itemDatas[] = new ItemData(
				$itemName->getName(),
				$movesetRatedItem->getPercent()
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
