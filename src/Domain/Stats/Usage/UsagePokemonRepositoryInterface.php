<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface UsagePokemonRepositoryInterface
{
    /**
     * Do any usage Pokémon records exist for this month and format?
     */
    public function hasAny(DateTimeInterface $month, FormatId $formatId): bool;

    /**
     * Save a usage Pokémon record.
     */
    public function save(UsagePokemon $usagePokemon): void;
}
