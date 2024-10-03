<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

final readonly class EggGroup
{
	public function __construct(
		private EggGroupId $id,
		private string $identifier,
	) {}

	public function getId() : EggGroupId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
