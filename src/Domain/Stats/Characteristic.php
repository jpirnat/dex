<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class Characteristic
{
	/** @var CharacteristicId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var StatId $highestStatId */
	private $highestStatId;

	/** @var int $ivModFive */
	private $ivModFive;

	/**
	 * Constructor.
	 *
	 * @param CharacteristicId $characteristicId
	 * @param string $identifier
	 * @param StatId $highestStatId
	 * @param int $ivModFive
	 */
	public function __construct(
		CharacteristicId $characteristicId,
		string $identifier,
		StatId $highestStatId,
		int $ivModFive
	) {
		$this->id = $characteristicId;
		$this->identifier = $identifier;
		$this->highestStatId = $highestStatId;
		$this->ivModFive = $ivModFive;
	}

	/**
	 * Get the characteristic's id.
	 *
	 * @return CharacteristicId
	 */
	public function getId() : CharacteristicId
	{
		return $this->id;
	}

	/**
	 * Get the characteristic's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the characteristic's highest stat id.
	 *
	 * @return StatId
	 */
	public function getHighestStatId() : StatId
	{
		return $this->highestStatId;
	}

	/**
	 * Get the characteristic's highest stat's IV mod five value.
	 *
	 * @return int
	 */
	public function getIvModFive() : int
	{
		return $this->ivModFive;
	}
}
