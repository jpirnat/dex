<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\VersionGroupId;

interface TypeRepositoryInterface
{
	/**
	 * Get a type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(TypeId $typeId) : Type;

	/**
	 * Get a type by its identifier.
	 *
	 * @throws TypeNotFoundException if no type exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Type;

	/**
	 * Get a type by its hidden power index.
	 *
	 * @throws TypeNotFoundException if no type exists with this hidden power
	 *     index.
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type;

	/**
	 * Get the main types available in this version group.
	 *
	 * @return Type[] Indexed by id.
	 */
	public function getMainByVersionGroup(VersionGroupId $versionGroupId) : array;
}
