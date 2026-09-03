<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Repositories;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface ShowdownFormatRepositoryInterface
{
    /**
     * Is the Pokémon Showdown format name known and imported?
     */
    public function isImported(DateTimeInterface $month, string $showdownFormatName): bool;

    /**
     * Is the Pokémon Showdown format name known and ignored?
     */
    public function isIgnored(DateTimeInterface $month, string $showdownFormatName): bool;

    /**
     * Is the Pokémon Showdown format name known?
     */
    public function isKnown(DateTimeInterface $month, string $showdownFormatName): bool;

    /**
     * Add a Pokémon Showdown format name to the list of unknown formats.
     */
    public function addUnknown(DateTimeInterface $month, string $showdownFormatName): void;

    /**
     * Get the format id of a Pokémon Showdown format name.
     *
     * @throws FormatNotImportedException if $showdownFormatName is not an
     *     imported format name.
     */
    public function getFormatId(DateTimeInterface $month, string $showdownFormatName): FormatId;

    /**
     * Get the names of the unknown formats the repository has tracked.
     *
     * @return string[][]
     */
    public function getUnknown(): array;
}
