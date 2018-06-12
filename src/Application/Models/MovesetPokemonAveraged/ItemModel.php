<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonAveraged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface;

class ItemModel
{
	/** @var MovesetRatedAveragedItemRepositoryInterface $movesetRatedAveragedItemRepository */
	private $movesetRatedAveragedItemRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var ItemData[] $itemDatas */
	private $itemDatas;

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAveragedItemRepositoryInterface $movesetRatedAveragedItemRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param ItemRepositoryInterface $itemRepository
	 */
	public function __construct(
		MovesetRatedAveragedItemRepositoryInterface $movesetRatedAveragedItemRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		ItemRepositoryInterface $itemRepository
	) {
		$this->movesetRatedAveragedItemRepository = $movesetRatedAveragedItemRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->itemRepository = $itemRepository;
	}

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
