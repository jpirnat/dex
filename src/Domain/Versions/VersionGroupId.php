<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

final class VersionGroupId extends EntityId
{
	public const RED_GREEN = 1;
	public const BLUE = 2;
	public const RED_BLUE = 3;
	public const YELLOW = 4;

	private const GOLD_SILVER = 5;
	private const RUBY_SAPPHIRE = 7;

	public function hasMoveDescriptions() : bool
	{
		return $this->value() >= self::GOLD_SILVER;
	}

	public function hasAbilities() : bool
	{
		return $this->value() >= self::RUBY_SAPPHIRE;
	}
}
