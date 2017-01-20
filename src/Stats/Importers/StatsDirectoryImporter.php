<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class StatsDirectoryImporter
{
	/** @var MonthDirectoryImporter $monthDirectoryImporter */
	protected $monthDirectoryImporter;

	/**
	 * Constructor.
	 *
	 * @param MonthDirectoryImporter $monthDirectoryImporter
	 */
	public function __construct(
		MonthDirectoryImporter $monthDirectoryImporter
	) {
		$this->monthDirectoryImporter = $monthDirectoryImporter;
	}

	/**
	 * Import all month directories in this stats directory.
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
