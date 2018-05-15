<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\MonthExtractor;
use Jp\Dex\Domain\Stats\Showdown\ShowdownFormatRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryParser
{
	/** @var LeadsFileParser $leadsFileParser */
	private $leadsFileParser;

	/** @var MonthExtractor $monthExtractor */
	private $monthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	private $formatRatingExtractor;

	/** @var ShowdownFormatRepositoryInterface $showdownFormatRepository */
	private $showdownFormatRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileParser $leadsFileParser
	 * @param MonthExtractor $monthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepositoryInterface $showdownFormatRepository
	 */
	public function __construct(
		LeadsFileParser $leadsFileParser,
		MonthExtractor $monthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepositoryInterface $showdownFormatRepository
	) {
		$this->leadsFileParser = $leadsFileParser;
		$this->monthExtractor = $monthExtractor;
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

		// Get the month from the leads directory url.
		$month = $this->monthExtractor->extractMonth($url);

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			if (!$this->showdownFormatRepository->isKnown($month, $showdownFormatName)) {
				$this->showdownFormatRepository->addUnknown($month, $showdownFormatName);
			}

			// Create a stream to read the moveset file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Parse the leads file.
			$this->leadsFileParser->parse($stream);
		}
	}
}
