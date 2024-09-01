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
	private const COLOSSEUM = 101;
	private const XD = 102;

	public function hasAbilities() : bool
	{
		return $this->id >= self::RUBY_SAPPHIRE
			&& $this->id !== self::LETS_GO_PIKACHU_EEVEE
			&& $this->id !== self::LEGENDS_ARCEUS
		;
	}

	public function hasHeldItems() : bool
	{
		return $this->id >= self::GOLD_SILVER
			&& $this->id !== self::LETS_GO_PIKACHU_EEVEE
			&& $this->id !== self::LEGENDS_ARCEUS
		;
	}

	public function hasItemDescriptions() : bool
	{
		return $this->id >= self::GOLD_SILVER;
	}

	public function hasItemIcons() : bool
	{
		return $this->id >= self::RUBY_SAPPHIRE
			&& $this->id !== self::COLOSSEUM
			&& $this->id !== self::XD
		;
	}

	public function hasMoveDescriptions() : bool
	{
		return $this->id >= self::GOLD_SILVER;
	}

	public function hasNatures() : bool
	{
		return $this->id >= self::RUBY_SAPPHIRE;
	}

	public function hasSpecialStat() : bool
	{
		return $this->id <= self::YELLOW;
	}

	public function hasTeraTypes() : bool
	{
		return $this->id === self::SCARLET_VIOLET;
	}
}
