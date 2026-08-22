<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\SmogonStats\Repositories\NatureNotImportedException;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureId;

final class SimpleShowdownNatureRepository implements ShowdownNatureRepositoryInterface
{
    /** @var NatureId[] $naturesToImport */
    private array $naturesToImport;

    /** @var array<string, int> $naturesToIgnore */
    private array $naturesToIgnore;

    /** @var string[] $unknownNatures */
    private array $unknownNatures = [];


    public function __construct()
    {
        $this->naturesToImport = [
            'Hardy' => new NatureId(1),
            'Lonely' => new NatureId(2),
            'Brave' => new NatureId(3),
            'Adamant' => new NatureId(4),
            'Naughty' => new NatureId(5),
            'Bold' => new NatureId(6),
            'Docile' => new NatureId(7),
            'Relaxed' => new NatureId(8),
            'Impish' => new NatureId(9),
            'Lax' => new NatureId(10),
            'Timid' => new NatureId(11),
            'Hasty' => new NatureId(12),
            'Serious' => new NatureId(13),
            'Jolly' => new NatureId(14),
            'Naive' => new NatureId(15),
            'Modest' => new NatureId(16),
            'Mild' => new NatureId(17),
            'Quiet' => new NatureId(18),
            'Bashful' => new NatureId(19),
            'Rash' => new NatureId(20),
            'Calm' => new NatureId(21),
            'Gentle' => new NatureId(22),
            'Sassy' => new NatureId(23),
            'Careful' => new NatureId(24),
            'Quirky' => new NatureId(25),
        ];

        $this->naturesToIgnore = [
            'Other' => 1,
        ];
    }

    /**
     * Is the Pokémon Showdown nature name known and imported?
     */
    public function isImported(string $showdownNatureName): bool
    {
        return isset($this->naturesToImport[$showdownNatureName]);
    }

    /**
     * Is the Pokémon Showdown nature name known and ignored?
     */
    public function isIgnored(string $showdownNatureName): bool
    {
        return isset($this->naturesToIgnore[$showdownNatureName]);
    }

    /**
     * Is the Pokémon Showdown nature name known?
     */
    public function isKnown(string $showdownNatureName): bool
    {
        return $this->isImported($showdownNatureName)
            || $this->isIgnored($showdownNatureName)
        ;
    }

    /**
     * Add a Pokémon Showdown nature name to the list of unknown natures.
     */
    public function addUnknown(string $showdownNatureName): void
    {
        $this->unknownNatures[$showdownNatureName] = $showdownNatureName;
    }

    /**
     * Get the nature id of a Pokémon Showdown nature name.
     *
     * @throws NatureNotImportedException if $showdownNatureName is not an
     *     imported nature name.
     */
    public function getNatureId(string $showdownNatureName): NatureId
    {
        // If the nature is imported, return the nature id.
        if ($this->isImported($showdownNatureName)) {
            return $this->naturesToImport[$showdownNatureName];
        }

        // If the nature is not known, add it to the list of unknown natures.
        if (!$this->isKnown($showdownNatureName)) {
            $this->addUnknown($showdownNatureName);
        }

        throw new NatureNotImportedException(
            "Nature should not be imported: $showdownNatureName."
        );
    }

    /**
     * Get the names of the unknown natures the repository has tracked.
     *
     * @return string[]
     */
    public function getUnknown(): array
    {
        return $this->unknownNatures;
    }
}
