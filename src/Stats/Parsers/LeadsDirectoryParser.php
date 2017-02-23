<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Importers\Extractors\FormatRatingExtractor;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Symfony\Component\DomCrawler\Crawler;

class LeadsDirectoryParser
{
	/** @var LeadsFileParser $leadsFileParser */
	protected $leadsFileParser;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var ShowdownFormatRepository $showdownFormatRepository */
	protected $showdownFormatRepository;

	/**
	 * Constructor.
	 *
	 * @param LeadsFileParser $leadsFileParser
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepository $showdownFormatRepository
	 */
	public function __construct(
		LeadsFileParser $leadsFileParser,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepository $showdownFormatRepository
	) {
		$this->leadsFileParser = $leadsFileParser;
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

		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			if (!$this->showdownFormatRepository->isKnown($showdownFormatName)) {
				$this->showdownFormatRepository->addUnknown($showdownFormatName);
			}

			// Create a stream to read the moveset file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Parse the leads file.
			$this->leadsFileParser->parse($stream);
		}
	}
}
