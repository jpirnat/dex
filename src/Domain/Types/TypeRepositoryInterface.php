<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

interface TypeRepositoryInterface
{
	/**
	 * Get a type by its id.
	 *
	 * @param TypeId $typeId
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 *
	 * @return Type
	 */
	public function getById(TypeId $typeId) : Type;

	/**
	 * Get a type by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws TypeNotFoundException if no type exists with this identifier.
	 *
	 * @return Type
	 */
	public function getByIdentifier(string $identifier) : Type;

	/**
	 * Get a type by its hidden power index.
	 *
	 * @param int $hiddenPowerIndex
	 *
	 * @throws TypeNotFoundException if no type exists with this hidden power
	 *     index.
	 *
	 * @return Type
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type;

	/**
	 * Get the main types available in this generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return Type[] Indexed by id.
	 */
	public function getMainByGeneration(GenerationId $generationId) : array;
}
