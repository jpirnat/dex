<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Versions\Generation;

class TypeEffectiveness
{
	/** @var Generation $generation */
	private $generation;

	/** @var TypeId $attackingTypeId */
	private $attackingTypeId;

	/** @var TypeId $defendingTypeId */
	private $defendingTypeId;

	/** @var float $factor */
	private $factor;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param TypeId $attackingTypeId
	 * @param TypeId $defendingTypeId
	 * @param float $factor
	 */
	public function __construct(
		Generation $generation,
		TypeId $attackingTypeId,
		TypeId $defendingTypeId,
		float $factor
	) {
		$this->generation = $generation;
		$this->attackingTypeId = $attackingTypeId;
		$this->defendingTypeId = $defendingTypeId;
		$this->factor = $factor;
	}

	/**
	 * Get the type effectiveness's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
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
