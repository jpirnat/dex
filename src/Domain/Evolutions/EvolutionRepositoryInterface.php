<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

interface EvolutionRepositoryInterface
{
	/**
	 * Get all evolutions.
	 *
	 * @return Evolution[]
	 */
	public function getAll() : array;
}
