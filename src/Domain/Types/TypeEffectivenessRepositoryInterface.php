<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

interface TypeEffectivenessRepositoryInterface
{
	/**
	 * Get type effectivenesses by generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return TypeEffectiveness[]
	 */
	public function getByGeneration(GenerationId $generationId) : array;
}
