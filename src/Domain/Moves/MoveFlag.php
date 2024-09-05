<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

final readonly class MoveFlag
{
	public function __construct(
		private MoveFlagId $id,
		private string $identifier,
	) {}

	public function getId() : MoveFlagId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
