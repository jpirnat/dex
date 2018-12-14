<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

interface SpeciesRepositoryInterface
{
	/**
	 * Get a species by its id.
	 *
	 * @param SpeciesId $speciesId
	 *
	 * @throws SpeciesNotFoundException if no species exists with this id.
	 *
	 * @return Species
	 */
	public function getById(SpeciesId $speciesId) : Species;
}
