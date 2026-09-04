<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsPokemonRepositoryInterface
{
    /**
     * Do any leads Pokémon records exist for this month and format?
     */
    public function hasAny(DateTimeInterface $month, FormatId $formatId): bool;

    /**
     * Save a leads Pokémon record.
     */
    public function save(LeadsPokemon $leadsPokemon): void;
}
