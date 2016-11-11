<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers;

use GuzzleHttp\Client;
use Jp\Trendalyzer\Importers\Extractors\FormatRatingExtractor;
use Jp\Trendalyzer\Importers\Extractors\YearMonthExtractor;
use Jp\Trendalyzer\Repositories\FormatsRepository;
use Symfony\Component\DomCrawler\Crawler;

class MonthDirectoryImporter
{
	/** @var UsageFileImporter $usageFileImporter */
	protected $usageFileImporter;

	/** @var LeadsDirectoryImporter $leadsDirectoryImporter */
	protected $leadsDirectoryImporter;

	/** @var MovesetDirectoryImporter $movesetDirectoryImporter */
	protected $movesetDirectoryImporter;

	/** @var YearMonthExtractor $yearMonthExtractor */
	protected $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var FormatsRepository $formatsRepository */
	protected $formatsRepository;

	/**
	 * Constructor.
	 *
	 * @param UsageFileImporter $usageFileImporter
	 * @param LeadsDirectoryImporter $leadsDirectoryImporter
	 * @param MovesetDirectoryImporter $movesetDirectoryImporter
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param FormatsRepository $formatsRepository
	 */
	public function __construct(
		UsageFileImporter $usageFileImporter,
		LeadsDirectoryImporter $leadsDirectoryImporter,
		MovesetDirectoryImporter $movesetDirectoryImporter,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		FormatsRepository $formatsRepository
	) {
		$this->usageFileImporter = $usageFileImporter;
		$this->leadsDirectoryImporter = $leadsDirectoryImporter;
		$this->movesetDirectoryImporter = $movesetDirectoryImporter;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->formatsRepository = $formatsRepository;
	}

	/**
	 * Import all stat files in this month directory.
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

		// Get the HTML of the month directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the month directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the year and month from the month directory url.
		$yearMonth = $this->yearMonthExtractor->extractYearMonth($url);
		$year = $yearMonth->year();
		$month = $yearMonth->month();

		// Import each usage file.
		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$smogonFormatName = $formatRating->smogonFormatName();
			$rating = $formatRating->rating();

			// If the format is known, import the file.
			if ($this->formatsRepository->hasSmogonFormatName($smogonFormatName)) {
				// Create a stream to read the usage file.
				$stream = $client->request('GET', $link->getUri())->getBody();

				// Get the format id from the Smogon format name.
				$formatId = $this->formatsRepository->getFormatId($smogonFormatName);

				// Import the usage file.
				$this->usageFileImporter->import(
					$stream,
					$year,
					$month,
					$formatId,
					$rating
				);
			}
		}

		// Import each leads file.
		$this->leadsDirectoryImporter->import($url . 'leads/');

		// Import each moveset file.
		$this->movesetDirectoryImporter->import($url . 'moveset/');
	}
}
