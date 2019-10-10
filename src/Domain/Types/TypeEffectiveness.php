<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\GenerationId;

final class TypeEffectiveness
{
	/** @var GenerationId $generationId */
	private $generationId;

	/** @var TypeId $attackingTypeId */
	private $attackingTypeId;

	/** @var TypeId $defendingTypeId */
	private $defendingTypeId;

	/** @var float $factor */
	private $factor;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $attackingTypeId
	 * @param TypeId $defendingTypeId
	 * @param float $factor
	 */
	public function __construct(
		GenerationId $generationId,
		TypeId $attackingTypeId,
		TypeId $defendingTypeId,
		float $factor
	) {
		$this->generationId = $generationId;
		$this->attackingTypeId = $attackingTypeId;
		$this->defendingTypeId = $defendingTypeId;
		$this->factor = $factor;
	}

	/**
	 * Get the type effectiveness's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the type effectiveness's attacking type id.
	 *
	 * @return TypeId
	 */
	public function getAttackingTypeId() : TypeId
	{
		return $this->attackingTypeId;
	}

	/**
	 * Get the type effectiveness's defending type id.
	 *
	 * @return TypeId
	 */
	public function getDefendingTypeId() : TypeId
	{
		return $this->defendingTypeId;
	}

	/**
	 * Get the type effectiveness's factor.
	 *
	 * @return float
	 */
	public function getFactor() : float
	{
		return $this->factor;
	}
}
