<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

final readonly class StatsDirectoryImporter
{
	public function __construct(
		private MonthDirectoryImporter $monthDirectoryImporter,
	) {}

	/**
	 * Import all month directories in this stats directory.
	 */
	public function import(string $url) : void
	{
		// Create the HTTP client.
		$client = new Client([
			'base_uri' => $url,
		]);

		// Get the HTML of the stats directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the stats directory page.
		$links = $crawler->filterXPath('//a[@href != "../"]')->links();

		// Import each month directory.
		foreach ($links as $link) {
			$this->monthDirectoryImporter->import($link->getUri());
		}
	}
}
