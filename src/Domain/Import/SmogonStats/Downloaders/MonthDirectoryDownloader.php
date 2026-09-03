<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Downloaders;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\DomCrawler\Crawler;

final readonly class MonthDirectoryDownloader
{
    public function __construct(
        private Client $client,
        private Filesystem $filesystem,
    ) {}

    /**
     * Download all stat files in this month directory.
     */
    public function download(DateTimeImmutable $month): void
    {
        $yearMonth = $month->format('Y-m');
        $url = "https://www.smogon.com/stats/$yearMonth/";

        $this->realDownload($url);
        $this->realDownload("$url/leads/");
        $this->realDownload("$url/moveset/");
    }

    private function realDownload(string $url): void
    {
        // Get the HTML of the month directory page.
        try {
            $response = $this->client->request('GET', $url);
        } catch (GuzzleException) {
            echo "Error: Could not download $url\n";
            return;
        }
        $body = $response->getBody();
        $html = $body->getContents();

        // Create the DOM crawler.
        $crawler = new Crawler($html, $url);

        // Get all the links on the month directory page.
        $links = $crawler->filterXPath('//a[contains(@href, ".txt.gz")]')->links();

        foreach ($links as $link) {
            $url = $link->getUri();

            // Remove "https://www.smogon.com/stats/" from the url.
            $filename = str_replace('https://www.smogon.com/stats/', '', $url);
            $filename = mb_substr($filename, 0, -3); // Remove the ".gz"

            try {
                $response = $this->client->request('GET', $url);
            } catch (GuzzleException) {
                echo "Error: Could not download $url\n";
                return;
            }
            $gzData = $response->getBody()->getContents();
            $contents = gzdecode($gzData);

            try {
                $this->filesystem->write("ignore/stats-mirror/$filename", $contents);
            } catch (FilesystemException) {
                echo "Error: Could not write $filename\n";
                return;
            }
        }
    }
}
