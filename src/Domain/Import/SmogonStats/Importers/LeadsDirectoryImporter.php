<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Importers;

use DateTime;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Import\SmogonStats\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

final readonly class LeadsDirectoryImporter
{
    public function __construct(
        private Filesystem $filesystem,
        private LeadsFileImporter $leadsFileImporter,
        private FormatRatingExtractor $formatRatingExtractor,
        private ShowdownFormatRepositoryInterface $showdownFormatRepository,
        private FormatRepositoryInterface $formatRepository,
    ) {}

    /**
     * Import all leads files in this directory of leads files.
     */
    public function import(DateTimeImmutable $month): void
    {
        $yearMonth = $month->format('Y-m');
        try {
            $files = $this->filesystem->listContents("ignore/stats-mirror/$yearMonth/leads")
                ->sortByPath();
        } catch (FilesystemException) {
            echo "Error: Cannot read $yearMonth leads directory.\n";
            return;
        }

        /** @var StorageAttributes $file */
        foreach ($files as $file) {
            if (!$file->isFile()) {
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

            // If this is a non-singles format, skip it. As of 2018-02-05, any
            // leads files that exist for non-singles formats contain incorrect
            // data.
            $format = $this->formatRepository->getById(
                $formatId,
                new LanguageId(LanguageId::ENGLISH), // The language doesn't matter.
            );
            if ($format->fieldSize > 1) {
                continue;
            }

            // Create a stream to read the leads file.
            try {
                $resource = $this->filesystem->readStream($file->path());
            } catch (FilesystemException) {
                echo 'Error: Cannot read ' . $file->path() . "\n";
                return;
            }
            $stream = Utils::streamFor($resource);

            // Import the leads file.
            $this->leadsFileImporter->import(
                $stream,
                DateTime::createFromImmutable($month),
                $formatId,
                $rating,
            );
        }
    }
}
