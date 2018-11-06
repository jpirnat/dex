<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;

/**
 * This model is used to determine the generation being queried by the dex,
 * and also to get other generations for navigation purposes.
 */
class GenerationModel
{
	/** @var GenerationRepositoryInterface $generationRepository */
	private $generationRepository;


	/** @var Generation $generation */
	private $generation;


	/**
	 * Constructor.
	 *
	 * @param GenerationRepositoryInterface $generationRepository
	 */
	public function __construct(
		GenerationRepositoryInterface $generationRepository
	) {
		$this->generationRepository = $generationRepository;
	}

	/**
	 * Set the generation.
	 *
	 * @param string $identifier
	 *
	 * @return GenerationId
	 */
	public function setGeneration(string $identifier) : GenerationId
	{
		$this->generation = $this->generationRepository->getByIdentifier(
			$identifier
		);

		return $this->generation->getId();
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
}
