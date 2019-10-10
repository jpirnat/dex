<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetMoveTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class MovesetMoveTrendGenerator
{
	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var GenerationMoveRepositoryInterface $generationMoveRepository */
	private $generationMoveRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param GenerationMoveRepositoryInterface $generationMoveRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		GenerationMoveRepositoryInterface $generationMoveRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->generationMoveRepository = $generationMoveRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a moveset move trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return MovesetMoveTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId
	) : MovesetMoveTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);
		$moveName = $this->moveNameRepository->getByLanguageAndMove(
			$languageId,
			$moveId
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the move's type.
		$generationMove = $this->generationMoveRepository->getByGenerationAndMove(
			$format->getGenerationId(),
			$moveId
		);
		$moveType = $this->typeRepository->getById($generationMove->getTypeId());

		// Get the usage data.
		$movesetRatedMoves = $this->movesetRatedMoveRepository->getByFormatAndRatingAndPokemonAndMove(
			$format->getId(),
			$rating,
			$pokemonId,
			$moveId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$movesetRatedMoves,
			'getPercent',
			0
		);

		return new MovesetMoveTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$moveName,
			$pokemonType,
			$moveType,
			$trendPoints
		);
	}
}
