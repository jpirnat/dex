<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\Generation;

interface TypeEffectivenessRepositoryInterface
{
	/**
	 * Get type effectivenesses by generation.
	 *
	 * @param Generation $generation
	 *
	 * @return TypeEffectiveness[]
	 */
	public function getByGeneration(Generation $generation) : array;
}
