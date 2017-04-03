<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Importers\Extractors\FormatRatingExtractor;
use Jp\Dex\Stats\Importers\Extractors\YearMonthExtractor;
use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Symfony\Component\DomCrawler\Crawler;

class MonthDirectoryParser
{
	/** @var UsageFileParser $usageFileParser */
	protected $usageFileParser;

	/** @var LeadsDirectoryParser $leadsDirectoryParser */
	protected $leadsDirectoryParser;

	/** @var MovesetDirectoryParser $movesetDirectoryParser */
	protected $movesetDirectoryParser;

	/** @var YearMonthExtractor $yearMonthExtractor */
	protected $yearMonthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	protected $formatRatingExtractor;

	/** @var ShowdownFormatRepository $showdownFormatRepository */
	protected $showdownFormatRepository;

	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var ShowdownAbilityRepository $showdownAbilityRepository */
	protected $showdownAbilityRepository;

	/** @var ShowdownItemRepository $showdownItemRepository */
	protected $showdownItemRepository;

	/** @var ShowdownNatureRepository $showdownNatureRepository */
	protected $showdownNatureRepository;

	/** @var ShowdownMoveRepository $showdownMoveRepository */
	protected $showdownMoveRepository;

	/**
	 * Constructor.
	 *
	 * @param UsageFileParser $usageFileParser
	 * @param LeadsDirectoryParser $leadsDirectoryParser
	 * @param MovesetDirectoryParser $movesetDirectoryParser
	 * @param YearMonthExtractor $yearMonthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepository $showdownFormatRepository
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param ShowdownAbilityRepository $showdownAbilityRepository
	 * @param ShowdownItemRepository $showdownItemRepository
	 * @param ShowdownNatureRepository $showdownNatureRepository
	 * @param ShowdownMoveRepository $showdownMoveRepository
	 */
	public function __construct(
		UsageFileParser $usageFileParser,
		LeadsDirectoryParser $leadsDirectoryParser,
		MovesetDirectoryParser $movesetDirectoryParser,
		YearMonthExtractor $yearMonthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepository $showdownFormatRepository,
		ShowdownPokemonRepository $showdownPokemonRepository,
		ShowdownAbilityRepository $showdownAbilityRepository,
		ShowdownItemRepository $showdownItemRepository,
		ShowdownNatureRepository $showdownNatureRepository,
		ShowdownMoveRepository $showdownMoveRepository
	) {
		$this->usageFileParser = $usageFileParser;
		$this->leadsDirectoryParser = $leadsDirectoryParser;
		$this->movesetDirectoryParser = $movesetDirectoryParser;
		$this->yearMonthExtractor = $yearMonthExtractor;
		$this->formatRatingExtractor = $formatRatingExtractor;
		$this->showdownFormatRepository = $showdownFormatRepository;
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->showdownAbilityRepository = $showdownAbilityRepository;
		$this->showdownItemRepository = $showdownItemRepository;
		$this->showdownNatureRepository = $showdownNatureRepository;
		$this->showdownMoveRepository = $showdownMoveRepository;
	}

	/**
	 * Parse this month directory for unknown Showdown format names.
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

		// Get the HTML of the month directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the month directory page.
		$links = $crawler->filterXPath('//a[contains(@href, ".txt")]')->links();

		// Get the year and month from the moveset directory url.
		$yearMonth = $this->yearMonthExtractor->extractYearMonth($url);
		$year = $yearMonth->year();
		$month = $yearMonth->month();

		// Parse each usage file link.
		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			if (!$this->showdownFormatRepository->isKnown($year, $month, $showdownFormatName)) {
				$this->showdownFormatRepository->addUnknown($year, $month, $showdownFormatName);
			}

			// Create a stream to read the usage file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Parse the usage file.
			$this->usageFileParser->parse($stream);
		}

		// Parse each leads file.
		$this->leadsDirectoryParser->parse($url . 'leads/');

		// Parse each moveset file.
		$this->movesetDirectoryParser->parse($url . 'moveset/');
	}

	/**
	 * Return the list of unknown formats.
	 *
	 * @return string[]
	 */
	public function getUnknownFormats() : array
	{
		return $this->showdownFormatRepository->getUnknown();
	}

	/**
	 * Return the list of unknown Pokémon.
	 *
	 * @return string[]
	 */
	public function getUnknownPokemon() : array
	{
		return $this->showdownPokemonRepository->getUnknown();
	}

	/**
	 * Return the list of unknown abilities.
	 *
	 * @return string[]
	 */
	public function getUnknownAbilities() : array
	{
		return $this->showdownAbilityRepository->getUnknown();
	}

	/**
	 * Return the list of unknown items.
	 *
	 * @return string[]
	 */
	public function getUnknownItems() : array
	{
		return $this->showdownItemRepository->getUnknown();
	}

	/**
	 * Return the list of unknown natures.
	 *
	 * @return string[]
	 */
	public function getUnknownNatures() : array
	{
		return $this->showdownNatureRepository->getUnknown();
	}

	/**
	 * Return the list of unknown moves.
	 *
	 * @return string[]
	 */
	public function getUnknownMoves() : array
	{
		return $this->showdownMoveRepository->getUnknown();
	}
}
