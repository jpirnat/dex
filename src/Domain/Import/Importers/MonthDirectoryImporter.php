<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\MonthExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

final class MonthDirectoryImporter
{
	public function __construct(
		private UsageFileImporter $usageFileImporter,
		private LeadsDirectoryImporter $leadsDirectoryImporter,
		private MovesetDirectoryImporter $movesetDirectoryImporter,
		private MonthExtractor $monthExtractor,
		private FormatRatingExtractor $formatRatingExtractor,
		private ShowdownFormatRepositoryInterface $showdownFormatRepository,
	) {}

	/**
	 * Import all stat files in this month directory.
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function import(string $url) : void
	{
		// Create the HTTP client.
		$client = new Client([
			'base_uri' => $url,
		]);

		// Get the HTML of the month directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the month directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the month from the month directory url.
		$month = $this->monthExtractor->extractMonth($url);

		// Import each usage file.
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

			// Create a stream to read the usage file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Import the usage file.
			$this->usageFileImporter->import(
				$stream,
				$month,
				$formatId,
				$rating
			);
		}

		// Import each leads file.
		$this->leadsDirectoryImporter->import($url . 'leads/');

		// Import each moveset file.
		$this->movesetDirectoryImporter->import($url . 'moveset/');
	}
}
