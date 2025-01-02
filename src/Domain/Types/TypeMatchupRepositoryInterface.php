<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

interface TypeMatchupRepositoryInterface
{
	/**
	 * Get type matchups by generation.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByGeneration(GenerationId $generationId) : array;

	/**
	 * Get multipliers grouped by defending type.
	 *
	 * @return float[][] Indexed by defending type identifier, then by attacking
	 *     type identifier.
	 */
	public function getMultipliers(GenerationId $generationId) : array;

	/**
	 * Get type matchups by generation and attacking type.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByAttackingType(GenerationId $generationId, TypeId $typeId) : array;

	/**
	 * Get type matchups by generation and defending type.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByDefendingType(GenerationId $generationId, TypeId $typeId) : array;
}
