<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class AbilityFlag
{
	public function __construct(
		private AbilityFlagId $id,
		private string $identifier,
	) {}

	public function getId() : AbilityFlagId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
