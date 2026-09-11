<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves\Flags;

use Jp\Dex\Domain\EntityId;

final class MoveFlagId extends EntityId
{
    public const int CONTACT = 3;
    public const int PUNCHING = 10;
    public const int SOUND_BASED = 11;
    public const int DANCE = 19;
    public const int SLICING = 39;
    public const int WIND = 40;
    public const int POWDER = 33;
    public const int BALL_AND_BOMB = 35;
    public const int PULSE = 48;
    public const int BITING = 34;
    public const int EXPLOSIVE = 49;
    public const int MENTAL = 50;
    public const int HEALING = 15;
}
