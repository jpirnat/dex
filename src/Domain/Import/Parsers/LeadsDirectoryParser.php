<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\YearMonthExtractor;
use Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryParser
{
	/** @var LeadsFileParser $leadsFileParser */
	private $leadsFileParser;

	/** @var YearMonthExtractor $yearMonthExtractor */
	private $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	private $formatRatingExtractor;

	/** @var ShowdownFormatRepositoryInterface $showdownFormatRepository */
	private $showdownFormatRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileParser $leadsFileParser
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepositoryInterface $showdownFormatRepository
	 */
	public function __construct(
		LeadsFileParser $leadsFileParser,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepositoryInterface $showdownFormatRepository
	) {
		$this->leadsFileParser = $leadsFileParser;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatRepository = $showdownFormatRepository;
	}

	/**
	 * Parse all leads files in this directory of leads files.
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function parse(string $url) : void
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

		// Get the year and month from the moveset directory url.
		$yearMonth = $this->yearMonthExtractor->extractYearMonth($url);
		$year = $yearMonth->year();
		$month = $yearMonth->month();

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			if (!$this->showdownFormatRepository->isKnown($year, $month, $showdownFormatName)) {
				$this->showdownFormatRepository->addUnknown($year, $month, $showdownFormatName);
			}

			// Create a stream to read the moveset file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Parse the leads file.
			$this->leadsFileParser->parse($stream);
		}
	}
}
