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
    private const int COLOSSEUM = 8;
    private const int XD = 11;
    public const int ULTRA_SUN_ULTRA_MOON = 20;
    private const int LETS_GO_PIKACHU_EEVEE = 21;
    public const int SWORD_SHIELD = 22;
    private const int LEGENDS_ARCEUS = 24;
    public const int SCARLET_VIOLET = 25;
    public const int CHAMPIONS = 27;

    public function hasHeldItems(): bool
    {
        return $this->value >= self::GOLD_SILVER
            && $this->value !== self::LETS_GO_PIKACHU_EEVEE
            && $this->value !== self::LEGENDS_ARCEUS
        ;
    }

    public function hasItemDescriptions(): bool
    {
        return $this->value >= self::GOLD_SILVER;
    }

    public function hasItemIcons(): bool
    {
        return $this->value >= self::RUBY_SAPPHIRE
            && $this->value !== self::COLOSSEUM
            && $this->value !== self::XD
        ;
    }

    public function hasMoveDescriptions(): bool
    {
        return $this->value >= self::GOLD_SILVER;
    }

    public function hasTms(): bool
    {
        return $this->value !== self::LEGENDS_ARCEUS;
    }

    public function hasTeraTypes(): bool
    {
        return $this->value === self::SCARLET_VIOLET;
    }
}
