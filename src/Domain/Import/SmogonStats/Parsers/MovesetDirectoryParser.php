<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Parsers;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Import\SmogonStats\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\SmogonStats\Repositories\ShowdownFormatRepositoryInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

final readonly class MovesetDirectoryParser
{
    public function __construct(
        private Filesystem $filesystem,
        private MovesetFileParser $movesetFileParser,
        private FormatRatingExtractor $formatRatingExtractor,
        private ShowdownFormatRepositoryInterface $showdownFormatRepository,
    ) {}

    /**
     * Parse all moveset files in this directory of moveset files.
     */
    public function parse(DateTimeImmutable $month): void
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
            $filename = pathinfo($file->path())['filename'];
            $formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
            $showdownFormatName = $formatRating->showdownFormatName;

            // If the format is unknown, add it to the list of unknown formats.
            if (!$this->showdownFormatRepository->isKnown($month, $showdownFormatName)) {
                $this->showdownFormatRepository->addUnknown($month, $showdownFormatName);
            }

            // Create a stream to read the moveset file.
            try {
                $resource = $this->filesystem->readStream($file->path());
            } catch (FilesystemException) {
                echo 'Error: Cannot read ' . $file->path() . "\n";
                return;
            }
            $stream = Utils::streamFor($resource);

            // Parse the moveset file.
            $this->movesetFileParser->parse($stream);
        }
    }
}
