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

	public function getId() : CharacteristicId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getHighestStatId() : StatId
	{
		return $this->highestStatId;
	}

	public function getIvModFive() : int
	{
		return $this->ivModFive;
	}
}
