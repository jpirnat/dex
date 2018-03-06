<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItemRepositoryInterface;

class UsageItemTrendGenerator
{
	/** @var UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository */
	private $usageRatedPokemonItemRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var ItemNameRepositoryInterface $itemNameRepository */
	private $itemNameRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param ItemNameRepositoryInterface $itemNameRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		UsageRatedPokemonItemRepositoryInterface $usageRatedPokemonItemRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		ItemNameRepositoryInterface $itemNameRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->usageRatedPokemonItemRepository = $usageRatedPokemonItemRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->itemNameRepository = $itemNameRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a usage item trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 * @param LanguageId $languageId
	 *
	 * @return UsageItemTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
		LanguageId $languageId
	) : UsageItemTrendLine {
		// Get the usage data.
		$usageRatedPokemonItems = $this->usageRatedPokemonItemRepository->getByFormatAndRatingAndPokemonAndItem(
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
