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
use Jp\Dex\Domain\Stats\Trends\Lines\UsageMoveTrendLine;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class UsageMoveTrendGenerator
{
	private UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository;
	private PokemonNameRepositoryInterface $pokemonNameRepository;
	private MoveNameRepositoryInterface $moveNameRepository;
	private PokemonTypeRepositoryInterface $pokemonTypeRepository;
	private TypeRepositoryInterface $typeRepository;
	private GenerationMoveRepositoryInterface $generationMoveRepository;
	private TrendPointCalculator $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param GenerationMoveRepositoryInterface $generationMoveRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		UsageRatedPokemonMoveRepositoryInterface $usageRatedPokemonMoveRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		GenerationMoveRepositoryInterface $generationMoveRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->usageRatedPokemonMoveRepository = $usageRatedPokemonMoveRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->generationMoveRepository = $generationMoveRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a usage move trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return UsageMoveTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
		LanguageId $languageId
	) : UsageMoveTrendLine {
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
		$usageRatedPokemonMoves = $this->usageRatedPokemonMoveRepository->getByFormatAndRatingAndPokemonAndMove(
			$format->getId(),
			$rating,
			$pokemonId,
			$moveId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$usageRatedPokemonMoves,
			'getUsagePercent',
			0
		);

		return new UsageMoveTrendLine(
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
