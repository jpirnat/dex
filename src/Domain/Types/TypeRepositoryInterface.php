<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

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
}
