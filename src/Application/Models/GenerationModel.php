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
	private Generation $generation;

	/** @var Generation[] $generations */
	private array $generations = [];


	public function __construct(
		private GenerationRepositoryInterface $generationRepository,
		private VersionGroupRepositoryInterface $versionGroupRepository,
	) {}


	/**
	 * Set the generation by its id.
	 */
	public function setById(GenerationId $generationId) : GenerationId
	{
		$this->generation = $this->generationRepository->getById($generationId);

		return $this->generation->getId();
	}

	/**
	 * Set the generation by its identifier.
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
	 */
	public function setWithPokemon(PokemonId $pokemonId) : void
	{
		$this->generations = $this->generationRepository->getWithPokemon($pokemonId);
	}

	/**
	 * Set the navigable generations to all generations this move has appeared in.
	 */
	public function setWithMove(MoveId $moveId) : void
	{
		$this->generations = $this->generationRepository->getWithMove($moveId);
	}

	/**
	 * Set the navigable generations to all generations since the given generation.
	 */
	public function setGensSince(GenerationId $generationId) : void
	{
		$this->generations = $this->generationRepository->getSince($generationId);
	}

	/**
	 * Set the navigable generations to all generations since the given version
	 * group's generation.
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
