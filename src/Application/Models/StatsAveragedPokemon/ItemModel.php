<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface;

final class ItemModel
{
	/** @var ItemData[] $itemDatas */
	private array $itemDatas = [];


	public function __construct(
		private MovesetRatedAveragedItemRepositoryInterface $movesetRatedAveragedItemRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private ItemRepositoryInterface $itemRepository,
	) {}


	/**
	 * Get moveset data averaged over multiple months.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated averaged item records for these months.
		$movesetRatedAveragedItems = $this->movesetRatedAveragedItemRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each item's data.
		foreach ($movesetRatedAveragedItems as $movesetRatedAveragedItem) {
			$itemId = $movesetRatedAveragedItem->getItemId();

			// Get this item's name.
			$itemName = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$itemId
			);

			// Get this item.
			$item = $this->itemRepository->getById($itemId);

			$this->itemDatas[] = new ItemData(
				$itemName->getName(),
				$item->getIdentifier(),
				$movesetRatedAveragedItem->getPercent()
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
