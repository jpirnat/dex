<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\YearMonthExtractor;
use Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryImporter
{
	/** @var LeadsFileImporter $leadsFileImporter */
	private $leadsFileImporter;

	/** @var YearMonthExtractor $yearMonthExtractor */
	private $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	private $formatRatingExtractor;

	/** @var ShowdownFormatRepositoryInterface $showdownFormatRepository */
	private $showdownFormatRepository;

	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileImporter $leadsFileImporter
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepositoryInterface $showdownFormatRepository
	 * @param FormatRepositoryInterface $formatRepository
	 */
	public function __construct(
		LeadsFileImporter $leadsFileImporter,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepositoryInterface $showdownFormatRepository,
		FormatRepositoryInterface $formatRepository
	) {
		$this->leadsFileImporter = $leadsFileImporter;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatRepository = $showdownFormatRepository;
		$this->formatRepository = $formatRepository;
	}

	/**
	 * Import all leads files in this directory of leads files.
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

		// Get the HTML of the leads directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the leads directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the year and month from the leads directory url.
		$yearMonth = $this->yearMonthExtractor->extractYearMonth($url);
		$year = $yearMonth->getYear();
		$month = $yearMonth->getMonth();

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();
			$rating = $formatRating->rating();

			// If this format is not meant to be imported, skip it.
			if (!$this->showdownFormatRepository->isImported($year, $month, $showdownFormatName)) {
				continue;
			}

			// Get the format id from the Pokémon Showdown format name.
			$formatId = $this->showdownFormatRepository->getFormatId($year, $month, $showdownFormatName);

			// If this is a non-singles format, skip it. As of 2018-02-05, any
			// leads files that exist for non-singles formats contain incorrect
			// data.
			$format = $this->formatRepository->getById($formatId);
			if ($format->getFieldSize() > 1) {
				continue;
			}

			// Create a stream to read the leads file.
			$stream = $client->request('GET', $link->getUri())->getBody();

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
