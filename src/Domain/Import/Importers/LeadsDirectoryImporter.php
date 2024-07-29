<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\MonthExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Symfony\Component\DomCrawler\Crawler;

final readonly class LeadsDirectoryImporter
{
	public function __construct(
		private LeadsFileImporter $leadsFileImporter,
		private MonthExtractor $monthExtractor,
		private FormatRatingExtractor $formatRatingExtractor,
		private ShowdownFormatRepositoryInterface $showdownFormatRepository,
		private FormatRepositoryInterface $formatRepository,
	) {}

	/**
	 * Import all leads files in this directory of leads files.
	 */
	public function import(string $url) : void
	{
		// Create the HTTP client.
		$client = new Client([
			'base_uri' => $url,
		]);

		// Get the HTML of the leads directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the leads directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")][not(contains(@href, ".txt.gz"))]')->links();

		// Get the month from the leads directory url.
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

			// If this is a non-singles format, skip it. As of 2018-02-05, any
			// leads files that exist for non-singles formats contain incorrect
			// data.
			$format = $this->formatRepository->getById(
				$formatId,
				new LanguageId(LanguageId::ENGLISH), // The language doesn't matter.
			);
			if ($format->getFieldSize() > 1) {
				continue;
			}

			// Create a stream to read the leads file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Import the leads file.
			$this->leadsFileImporter->import(
				$stream,
				$month,
				$formatId,
				$rating,
			);
		}
	}
}
