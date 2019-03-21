<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

interface VersionGroupRepositoryInterface
{
	/**
	 * Get a version group by its id.
	 *
	 * @param VersionGroupId $versionGroupId
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 *
	 * @return VersionGroup
	 */
	public function getById(VersionGroupId $versionGroupId) : VersionGroup;

	/**
	 * Get a version group by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 *
	 * @return VersionGroup
	 */
	public function getByIdentifier(string $identifier) : VersionGroup;

	/**
	 * Get version groups between these two generations, inclusive. This method
	 * is used to get all relevant version groups for the dex Pokémon page.
	 *
	 * @param GenerationId $begin
	 * @param GenerationId $end
	 *
	 * @return VersionGroup[] Indexed by id, ordered by sort.
	 */
	public function getBetween(GenerationId $begin, GenerationId $end) : array;
}
