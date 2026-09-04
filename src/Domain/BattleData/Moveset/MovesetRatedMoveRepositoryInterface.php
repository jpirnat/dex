<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

interface MovesetRatedMoveRepositoryInterface
{
    /**
     * Save a moveset rated move record.
     */
    public function save(MovesetRatedMove $movesetRatedMove): void;
}
