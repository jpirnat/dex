<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

interface MovesetRatedTeammateRepositoryInterface
{
    /**
     * Save a moveset rated teammate record.
     */
    public function save(MovesetRatedTeammate $movesetRatedTeammate): void;
}
