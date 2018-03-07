<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMoveRepositoryInterface;

class UsageMoveTrendGenerator
{
	/** @var UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository */
	private $usageRatedPokemonMoveRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->usageRatedPokemonMoveRepository = $usageRatedPokemonMoveRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a usage move trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return UsageMoveTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId
	) : UsageMoveTrendLine {
		// Get the name data.
		$formatName = $this->formatNameRepository->getByLanguageAndFormat(
			$languageId,
			$formatId
		);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);
		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$moveId
		);

		// Get the usage data.
		$usageRatedPokemonMoves = $this->usageRatedPokemonMoveRepository->getByFormatAndRatingAndPokemonAndMove(
			$formatId,
			$rating,
			$pokemonId,
			$moveId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$usageRatedPokemonMoves,
			'getUsagePercent',
			0
		);

		return new UsageMoveTrendLine(
			$formatName,
			$rating,
			$pokemonName,
			$moveName,
			$trendPoints
		);
	}
}
