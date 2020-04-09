<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

/**
 * This model is used to determine the generation being queried by the dex,
 * and also to get other generations for navigation purposes.
 */
final class GenerationModel
{
	private GenerationRepositoryInterface $generationRepository;
	private VersionGroupRepositoryInterface $versionGroupRepository;


	private Generation $generation;

	/** @var Generation[] $generations */
	private array $generations = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationRepositoryInterface $generationRepository
	 * @param VersionGroupRepositoryInterface $versionGroupRepository
	 */
	public function __construct(
		GenerationRepositoryInterface $generationRepository,
		VersionGroupRepositoryInterface $versionGroupRepository
	) {
		$this->generationRepository = $generationRepository;
		$this->versionGroupRepository = $versionGroupRepository;
	}

	/**
	 * Set the generation by its id.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return GenerationId
	 */
	public function setById(GenerationId $generationId) : GenerationId
	{
		$this->generation = $this->generationRepository->getById($generationId);

		return $this->generation->getId();
	}

	/**
	 * Set the generation by its identifier.
	 *
	 * @param string $generationIdentifier
	 *
	 * @return GenerationId
	 */
	public function setByIdentifier(string $generationIdentifier) : GenerationId
	{
		$this->generation = $this->generationRepository->getByIdentifier(
			$generationIdentifier
		);

		return $this->generation->getId();
	}

	/**
	 * Set the navigable generations to all generations this Pokémon has appeared in.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @return void
	 */
	public function setWithPokemon(PokemonId $pokemonId) : void
	{
		$this->generations = $this->generationRepository->getWithPokemon($pokemonId);
	}

	/**
	 * Set the navigable generations to all generations this move has appeared in.
	 *
	 * @param MoveId $moveId
	 *
	 * @return void
	 */
	public function setWithMove(MoveId $moveId) : void
	{
		$this->generations = $this->generationRepository->getWithMove($moveId);
	}

	/**
	 * Set the navigable generations to all generations since the given generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return void
	 */
	public function setGensSince(GenerationId $generationId) : void
	{
		$this->generations = $this->generationRepository->getSince($generationId);
	}

	/**
	 * Set the navigable generations to all generations since the given version
	 * group's generation.
	 *
	 * @param VersionGroupId $versionGroupId
	 *
	 * @return void
	 */
	public function setGensSinceVg(VersionGroupId $versionGroupId) : void
	{
		$versionGroup = $this->versionGroupRepository->getById($versionGroupId);
		$this->generations = $this->generationRepository->getSince(
			$versionGroup->getGenerationId()
		);
	}

	/**
	 * Get the generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the generations.
	 *
	 * @return Generation[]
	 */
	public function getGenerations() : array
	{
		return $this->generations;
	}
}
