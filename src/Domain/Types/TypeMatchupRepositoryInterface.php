<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

interface TypeMatchupRepositoryInterface
{
	/**
	 * Get type matchups by generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByGeneration(GenerationId $generationId) : array;
}
