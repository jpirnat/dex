<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads\Averaged;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsAveragedPokemonRepositoryInterface
{
    /**
     * Get leads averaged Pokémon records by their start month, end month, and
     * format.
     *
     * @return LeadsAveragedPokemon[] Indexed by Pokémon id.
     */
    public function getByMonthsAndFormat(
        DateTimeInterface $start,
        DateTimeInterface $end,
        FormatId $formatId,
    ): array;
}
