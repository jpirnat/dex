<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Trends;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;

class MovesetItemTrendGenerator
{
	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	private $movesetRatedItemRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 */
	public function __construct(
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		ItemNameRepositoryInterface $itemNameRepository
	) {
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->itemNameRepository = $itemNameRepository;
	}

	/**
	 * Get the data for a moveset item trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 * @param LanguageId $languageId
	 *
	 * @return MovesetItemTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
		LanguageId $languageId
	) : MovesetItemTrendLine {
		// Get the usage data.
		$movesetRatedItems = $this->movesetRatedItemRepository->getByFormatAndRatingAndPokemonAndItem(
			$formatId,
			$rating,
			$pokemonId,
			$itemId
		);

		// Get the name data.
		$formatName = $this->formatNameRepository->getByLanguageAndFormat($languageId, $formatId);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon($languageId, $pokemonId);
		$itemName = $this->itemNameRepository->getByLanguageAndItem($languageId, $itemId);
	}
}
