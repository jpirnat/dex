<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Importers\Extractors\FormatRatingExtractor;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Symfony\Component\DomCrawler\Crawler;

class MonthDirectoryParser
{
	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var ShowdownFormatRepository $showdownFormatRepository */
	protected $showdownFormatRepository;

	/**
	 * Constructor.
	 *
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepository $showdownFormatRepository
	 */
	public function __construct(
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepository $showdownFormatRepository
	) {
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatRepository = $showdownFormatRepository;
	}

	/**
	 * Parse this month directory for unknown Showdown format names.
	 *
	 * @param string $url
	 *
	 * @return string[]
	 */
	public function parse(string $url) : array
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

		// Parse each usage file link.
		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			if (!$this->showdownFormatRepository->isKnown($showdownFormatName)) {
				$this->showdownFormatRepository->addUnknown($showdownFormatName);
			}
		}

		// Return the list of unknown formats.
		return $this->showdownFormatRepository->getUnknown();
	}
}
