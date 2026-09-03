<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Parsers;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\SmogonStats\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownTypeRepositoryInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

final readonly class MonthDirectoryParser
{
    public function __construct(
        private Filesystem $filesystem,
        private UsageFileParser $usageFileParser,
        private LeadsDirectoryParser $leadsDirectoryParser,
        private MovesetDirectoryParser $movesetDirectoryParser,
        private FormatRatingExtractor $formatRatingExtractor,
        private ShowdownFormatRepositoryInterface $showdownFormatRepository,
        private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
        private ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
        private ShowdownItemRepositoryInterface $showdownItemRepository,
        private ShowdownNatureRepositoryInterface $showdownNatureRepository,
        private ShowdownMoveRepositoryInterface $showdownMoveRepository,
        private ShowdownTypeRepositoryInterface $showdownTypeRepository,
    ) {}

    /**
     * Parse this month directory for unknown Showdown format names.
     */
    public function parse(DateTimeImmutable $month): void
    {
        $yearMonth = $month->format('Y-m');
        try {
            $files = $this->filesystem->listContents("ignore/stats-mirror/$yearMonth")
                ->sortByPath();
        } catch (FilesystemException) {
            echo "Error: Cannot read $yearMonth stats directory.\n";
            return;
        }

        /** @var StorageAttributes $file */
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if (str_contains($file->path(), '/2016-10/cap-')) {
                // October 2016 CAP doesn't have valid usage files. The moveset
                // files seem to have been accidentally uploaded in their place.
                // https://www.smogon.com/stats/2016-10/cap-0.txt
                continue;
            }

            // Get the format and rating from the filename of the link.
            $filename = pathinfo($file->path())['filename'];
            $formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
            $showdownFormatName = $formatRating->showdownFormatName;

            // If the format is unknown, add it to the list of unknown formats.
            $formatUnknown = !$this->showdownFormatRepository->isKnown(
                $month,
                $showdownFormatName,
            );
            if ($formatUnknown) {
                $this->showdownFormatRepository->addUnknown($month, $showdownFormatName);
            }

            // Create a stream to read the usage file.
            try {
                $resource = $this->filesystem->readStream($file->path());
            } catch (FilesystemException) {
                echo 'Error: Cannot read ' . $file->path() . "\n";
                return;
            }
            $stream = Utils::streamFor($resource);

            // Parse the usage file.
            $totalBattles = $this->usageFileParser->parse($stream);

            // Keep track of which new formats should be ignored because they
            // don't have enough battles this month.
            $tooFewBattles = 0 <= $totalBattles && $totalBattles <= 100;
            if ($formatUnknown && $tooFewBattles) {
                $format = $formatRating->showdownFormatName;
                $rating = $formatRating->rating;
                echo "$yearMonth\t$format\t$rating\ttoo few battles: $totalBattles\n";
            }
        }

        // Parse each leads file.
        $this->leadsDirectoryParser->parse($month);

        // Parse each moveset file.
        $this->movesetDirectoryParser->parse($month);
    }

    /**
     * Return the list of unknown formats.
     *
     * @return string[][]
     */
    public function getUnknownFormats(): array
    {
        return $this->showdownFormatRepository->getUnknown();
    }

    /**
     * Return the list of unknown Pokémon.
     *
     * @return string[]
     */
    public function getUnknownPokemon(): array
    {
        return $this->showdownPokemonRepository->getUnknown();
    }

    /**
     * Return the list of unknown abilities.
     *
     * @return string[]
     */
    public function getUnknownAbilities(): array
    {
        return $this->showdownAbilityRepository->getUnknown();
    }

    /**
     * Return the list of unknown items.
     *
     * @return string[]
     */
    public function getUnknownItems(): array
    {
        return $this->showdownItemRepository->getUnknown();
    }

    /**
     * Return the list of unknown natures.
     *
     * @return string[]
     */
    public function getUnknownNatures(): array
    {
        return $this->showdownNatureRepository->getUnknown();
    }

    /**
     * Return the list of unknown moves.
     *
     * @return string[]
     */
    public function getUnknownMoves(): array
    {
        return $this->showdownMoveRepository->getUnknown();
    }

    /**
     * Return the list of unknown types.
     *
     * @return string[]
     */
    public function getUnknownTypes(): array
    {
        return $this->showdownTypeRepository->getUnknown();
    }
}
