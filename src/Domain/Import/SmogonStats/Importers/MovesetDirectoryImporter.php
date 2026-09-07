<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Importers;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\SmogonStats\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownFormatRepositoryInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

final readonly class MovesetDirectoryImporter
{
    public function __construct(
        private Filesystem $filesystem,
        private MovesetFileImporter $movesetFileImporter,
        private FormatRatingExtractor $formatRatingExtractor,
        private ShowdownFormatRepositoryInterface $showdownFormatRepository,
    ) {}

    /**
     * Import all moveset files in this directory of moveset files.
     */
    public function import(DateTimeImmutable $month): void
    {
        $yearMonth = $month->format('Y-m');
        try {
            $files = $this->filesystem->listContents("ignore/stats-mirror/$yearMonth/moveset")
                ->sortByPath();
        } catch (FilesystemException) {
            echo "Error: Cannot read $yearMonth moveset directory.\n";
            return;
        }

        /** @var StorageAttributes $file */
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            // Get the format and rating from the filename of the link.
            $filename = mb_substr($file->path(), mb_strlen("ignore/stats-mirror/$yearMonth/"));
            if ($filename === '.DS_Store') {
                continue;
            }
            $formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
            $showdownFormatName = $formatRating->showdownFormatName;
            $rating = $formatRating->rating;

            // If this format is not meant to be imported, skip it.
            if (!$this->showdownFormatRepository->isImported($month, $showdownFormatName)) {
                continue;
            }

            // Get the format id from the Pokémon Showdown format name.
            $formatId = $this->showdownFormatRepository->getFormatId($month, $showdownFormatName);

            // Create a stream to read the moveset file.
            try {
                $resource = $this->filesystem->readStream($file->path());
            } catch (FilesystemException) {
                echo 'Error: Cannot read ' . $file->path() . "\n";
                return;
            }
            $stream = Utils::streamFor($resource);

            // Import the moveset file.
            $this->movesetFileImporter->import(
                $stream,
                $month,
                $formatId,
                $rating,
            );
        }
    }
}
