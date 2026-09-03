<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Importers;

use DateTime;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\SmogonStats\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\TeammatesFixer;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

final readonly class MonthDirectoryImporter
{
    public function __construct(
        private Filesystem $filesystem,
        private UsageFileImporter $usageFileImporter,
        private LeadsDirectoryImporter $leadsDirectoryImporter,
        private MovesetDirectoryImporter $movesetDirectoryImporter,
        private FormatRatingExtractor $formatRatingExtractor,
        private ShowdownFormatRepositoryInterface $showdownFormatRepository,
        private TeammatesFixer $teammatesFixer,
    ) {}

    /**
     * Import all stat files in this month directory.
     */
    public function import(DateTimeImmutable $month): void
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
            $rating = $formatRating->rating;

            // If this format is not meant to be imported, skip it.
            if (!$this->showdownFormatRepository->isImported($month, $showdownFormatName)) {
                continue;
            }

            // Get the format id from the Pokémon Showdown format name.
            $formatId = $this->showdownFormatRepository->getFormatId($month, $showdownFormatName);

            // Create a stream to read the usage file.
            try {
                $resource = $this->filesystem->readStream($file->path());
            } catch (FilesystemException) {
                echo 'Error: Cannot read ' . $file->path() . "\n";
                return;
            }
            $stream = Utils::streamFor($resource);

            // Import the usage file.
            $this->usageFileImporter->import(
                $stream,
                DateTime::createFromImmutable($month),
                $formatId,
                $rating,
            );
        }

        // Import each leads file.
        $this->leadsDirectoryImporter->import($month);

        // Import each moveset file.
        $this->movesetDirectoryImporter->import($month);

        // Fix teammate percentages.
        $this->teammatesFixer->fixTeammates($month);
    }
}
