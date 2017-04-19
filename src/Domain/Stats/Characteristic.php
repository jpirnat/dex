<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class Characteristic
{
	/** @var CharacteristicId $id */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var StatId $highestStatId */
	protected $highestStatId;

	/** @var int $ivModFive */
	protected $ivModFive;

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
	public function id() : CharacteristicId
	{
		return $this->id;
	}

	/**
	 * Get the characteristic's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the characteristic's highest stat id.
	 *
	 * @return StatId
	 */
	public function highestStatId() : StatId
	{
		return $this->highestStatId;
	}

	/**
	 * Get the characteristic's highest stat's IV mod five value.
	 *
	 * @return int
	 */
	public function ivModFive() : int
	{
		return $this->ivModFive;
	}
}
