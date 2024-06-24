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

final readonly class ItemModel
{
	public function __construct(
		private MovesetRatedAveragedItemRepositoryInterface $movesetRatedAveragedItemRepository,
		private ItemNameRepositoryInterface $itemNameRepository,
		private ItemRepositoryInterface $itemRepository,
	) {}

	/**
	 * Set individual Pokémon item data averaged over multiple months.
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		// Get moveset rated averaged item records for these months.
		$movesetRatedAveragedItems = $this->movesetRatedAveragedItemRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId,
		);

		$items = [];

		// Get each item's data.
		foreach ($movesetRatedAveragedItems as $movesetRatedAveragedItem) {
			$itemId = $movesetRatedAveragedItem->getItemId();

			// Get this item's name.
			$itemName = $this->itemNameRepository->getByLanguageAndItem(
				$languageId,
				$itemId,
			);

			// Get this item.
			$item = $this->itemRepository->getById($itemId);

			$items[] = [
				'identifier' => $item->getIdentifier(),
				'name' => $itemName->getName(),
				'percent' => $movesetRatedAveragedItem->getPercent(),
			];
		}

		return $items;
	}
}
