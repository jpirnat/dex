<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Importers\Extractors\FormatRatingExtractor;
use Jp\Dex\Stats\Importers\Extractors\YearMonthExtractor;
use Jp\Dex\Stats\Repositories\ShowdownFormatsRepository;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryImporter
{
	/** @var LeadsFileImporter $leadsFileImporter */
	protected $leadsFileImporter;

	/** @var YearMonthExtractor $yearMonthExtractor */
	protected $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var ShowdownFormatsRepository $showdownFormatsRepository */
	protected $showdownFormatsRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileImporter $leadsFileImporter
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatsRepository $showdownFormatsRepository
	 */
	public function __construct(
		LeadsFileImporter $leadsFileImporter,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatsRepository $showdownFormatsRepository
	) {
		$this->leadsFileImporter = $leadsFileImporter;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatsRepository = $showdownFormatsRepository;
	}

	/**
	 * Import all leads files in this directory of leads files.
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function import(string $url)
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
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the year and month from the leads directory url.
		$yearMonth = $this->yearMonthExtractor->extractYearMonth($url);
		$year = $yearMonth->year();
		$month = $yearMonth->month();

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();
			$rating = $formatRating->rating();

			// If the format is known, import the file.
			if ($this->showdownFormatsRepository->isImported($showdownFormatName)) {
				// Create a stream to read the leads file.
				$stream = $client->request('GET', $link->getUri())->getBody();

				// Get the format id from the Pokémon Showdown format name.
				$formatId = $this->showdownFormatsRepository->getFormatId($showdownFormatName);

				// Import the leads file.
				$this->leadsFileImporter->import(
					$stream,
					$year,
					$month,
					$formatId,
					$rating
				);
			}
		}
	}
}
