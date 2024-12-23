<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\EntityId;

final class VersionGroupId extends EntityId
{
	public const int RED_GREEN = 1;
	public const int BLUE = 2;
	public const int RED_BLUE = 3;
	private const int GOLD_SILVER = 5;
	private const int RUBY_SAPPHIRE = 7;
	public const int ULTRA_SUN_ULTRA_MOON = 18;
	private const int LETS_GO_PIKACHU_EEVEE = 19;
	public const int SWORD_SHIELD = 20;
	private const int LEGENDS_ARCEUS = 22;
	public const int SCARLET_VIOLET = 23;
	private const int COLOSSEUM = 101;
	private const int XD = 102;

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

	public function hasTms() : bool
	{
		return $this->id !== self::LEGENDS_ARCEUS;
	}

	public function hasTeraTypes() : bool
	{
		return $this->id === self::SCARLET_VIOLET;
	}
}
