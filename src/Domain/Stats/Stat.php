<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

final readonly class Stat
{
	public function __construct(
		private StatId $id,
		private string $identifier,
	) {}

	public function getId() : StatId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
