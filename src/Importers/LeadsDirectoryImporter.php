<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers;

use GuzzleHttp\Client;
use Jp\Trendalyzer\Importers\Extractors\FormatRatingExtractor;
use Jp\Trendalyzer\Importers\Extractors\YearMonthExtractor;
use Jp\Trendalyzer\Repositories\FormatsRepository;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryImporter
{
	/** @var LeadsFileImporter $leadsFileImporter */
	protected $leadsFileImporter;

	/** @var YearMonthExtractor $yearMonthExtractor */
	protected $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var FormatsRepository $formatsRepository */
	protected $formatsRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileImporter $leadsFileImporter
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param FormatsRepository $formatsRepository
	 */
	public function __construct(
		LeadsFileImporter $leadsFileImporter,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		FormatsRepository $formatsRepository
	) {
		$this->leadsFileImporter = $leadsFileImporter;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->formatsRepository = $formatsRepository;
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
		$html = $client->request('GET', '/')->getBody()->getContents();

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
			$smogonFormatName = $formatRating->smogonFormatName();
			$rating = $formatRating->rating();

			// If the format is known, import the file.
			if ($this->formatsRepository->hasSmogonFormatName($smogonFormatName)) {
				// Create a stream to read the leads file.
				$stream = $client->request('GET', $link)->getBody();

				// Get the format id from the Smogon format name.
				$formatId = $this->formatsRepository->getFormatId($smogonFormatName);

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
