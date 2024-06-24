<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Characteristics;

use Jp\Dex\Domain\Stats\StatId;

final readonly class Characteristic
{
	public function __construct(
		private CharacteristicId $id,
		private string $identifier,
		private StatId $highestStatId,
		private int $ivModFive,
	) {}

	/**
	 * Get the characteristic's id.
	 */
	public function getId() : CharacteristicId
	{
		return $this->id;
	}

	/**
	 * Get the characteristic's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the characteristic's highest stat id.
	 */
	public function getHighestStatId() : StatId
	{
		return $this->highestStatId;
	}

	/**
	 * Get the characteristic's highest stat's IV mod five value.
	 */
	public function getIvModFive() : int
	{
		return $this->ivModFive;
	}
}
