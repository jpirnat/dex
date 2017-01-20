<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Importers\Extractors\FormatRatingExtractor;
use Jp\Dex\Stats\Importers\Extractors\YearMonthExtractor;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Symfony\Component\DomCrawler\Crawler;

class MovesetDirectoryImporter
{
	/** @var MovesetFileImporter $movesetFileImporter */
	protected $movesetFileImporter;

	/** @var YearMonthExtractor $yearMonthExtractor */
	protected $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var ShowdownFormatRepository $showdownFormatRepository */
	protected $showdownFormatRepository;

	/**
	 * Constructor.
	 *
	 * @param MovesetFileImporter $movesetFileImporter
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepository $showdownFormatRepository
	 */
	public function __construct(
		MovesetFileImporter $movesetFileImporter,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepository $showdownFormatRepository
	) {
		$this->movesetFileImporter = $movesetFileImporter;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatRepository = $showdownFormatRepository;
	}

	/**
	 * Import all moveset files in this directory of moveset files.
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

		// Get the HTML of the moveset directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the moveset directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the year and month from the moveset directory url.
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
			if ($this->showdownFormatRepository->isImported($showdownFormatName)) {
				// Create a stream to read the moveset file.
				$stream = $client->request('GET', $link->getUri())->getBody();

				// Get the format id from the Pokémon Showdown format name.
				$formatId = $this->showdownFormatRepository->getFormatId($showdownFormatName);

				// Import the moveset file.
				$this->movesetFileImporter->import(
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
