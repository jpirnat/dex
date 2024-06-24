<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\MonthExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

final readonly class MovesetDirectoryImporter
{
	public function __construct(
		private MovesetFileImporter $movesetFileImporter,
		private MonthExtractor $monthExtractor,
		private FormatRatingExtractor $formatRatingExtractor,
		private ShowdownFormatRepositoryInterface $showdownFormatRepository,
	) {}

	/**
	 * Import all moveset files in this directory of moveset files.
	 */
	public function import(string $url) : void
	{
		// Create the HTTP client.
		$client = new Client([
			'base_uri' => $url,
		]);

		// Get the HTML of the moveset directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the moveset directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the month from the moveset directory url.
		$month = $this->monthExtractor->extractMonth($url);

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();
			$rating = $formatRating->rating();

			// If this format is not meant to be imported, skip it.
			if (!$this->showdownFormatRepository->isImported($month, $showdownFormatName)) {
				continue;
			}

			// Get the format id from the Pokémon Showdown format name.
			$formatId = $this->showdownFormatRepository->getFormatId($month, $showdownFormatName);

			// Create a stream to read the moveset file.
			$stream = $client->request('GET', $link->getUri())->getBody();

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
