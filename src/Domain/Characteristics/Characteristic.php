<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Characteristics;

use Jp\Dex\Domain\Stats\StatId;

final class Characteristic
{
	public function __construct(
		private CharacteristicId $id,
		private string $identifier,
		private StatId $highestStatId,
		private int $ivModFive,
	) {}

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
