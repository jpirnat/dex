<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

interface SpeciesRepositoryInterface
{
	/**
	 * Get a species by its id.
	 *
	 * @throws SpeciesNotFoundException if no species exists with this id.
	 */
	public function getById(SpeciesId $speciesId) : Species;
}
