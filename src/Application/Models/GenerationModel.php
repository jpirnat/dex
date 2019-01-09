<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

/**
 * This model is used to determine the generation being queried by the dex,
 * and also to get other generations for navigation purposes.
 */
class GenerationModel
{
	/** @var GenerationRepositoryInterface $generationRepository */
	private $generationRepository;

	/** @var VersionGroupRepositoryInterface $versionGroupRepository */
	private $versionGroupRepository;


	/** @var Generation $generation */
	private $generation;

	/** @var Generation[] $generations */
	private $generations = [];


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
