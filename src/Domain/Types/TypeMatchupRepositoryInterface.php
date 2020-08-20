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

	/**
	 * Get type matchups by generation and attacking type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByAttackingType(GenerationId $generationId, TypeId $typeId) : array;

	/**
	 * Get type matchups by generation and defending type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByDefendingType(GenerationId $generationId, TypeId $typeId) : array;
}
