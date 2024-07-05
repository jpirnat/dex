<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

final class VersionGroupId extends EntityId
{
	public const RED_GREEN = 1;
	public const BLUE = 2;
	public const RED_BLUE = 3;

	private const YELLOW = 4;
	private const GOLD_SILVER = 5;
	private const RUBY_SAPPHIRE = 7;
	public const ULTRA_SUN_ULTRA_MOON = 18;
	private const LETS_GO_PIKACHU_EEVEE = 19;
	public const SWORD_SHIELD = 20;
	private const LEGENDS_ARCEUS = 22;
	public const SCARLET_VIOLET = 23;

	public function hasSpecialStat() : bool
	{
		return $this->value() <= self::YELLOW;
	}

	public function hasMoveDescriptions() : bool
	{
		return $this->value() >= self::GOLD_SILVER;
	}

	public function hasHeldItems() : bool
	{
		return $this->value() >= self::GOLD_SILVER
			&& $this->value() !== self::LETS_GO_PIKACHU_EEVEE
			&& $this->value() !== self::LEGENDS_ARCEUS
		;
	}

	public function hasAbilities() : bool
	{
		return $this->value() >= self::RUBY_SAPPHIRE
			&& $this->value() !== self::LETS_GO_PIKACHU_EEVEE
			&& $this->value() !== self::LEGENDS_ARCEUS
		;
	}

	public function hasNatures() : bool
	{
		return $this->value() >= self::RUBY_SAPPHIRE;
	}
}
