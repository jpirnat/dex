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
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;

class MovesetMoveTrendGenerator
{
	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

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
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a moveset move trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return MovesetMoveTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId
	) : MovesetMoveTrendLine {
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
		$movesetRatedMoves = $this->movesetRatedMoveRepository->getByFormatAndRatingAndPokemonAndMove(
			$formatId,
			$rating,
			$pokemonId,
			$moveId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$movesetRatedMoves,
			'getPercent',
			0
		);

		return new MovesetMoveTrendLine(
			$formatName,
			$rating,
			$pokemonName,
			$moveName,
			$trendPoints
		);
	}
}
