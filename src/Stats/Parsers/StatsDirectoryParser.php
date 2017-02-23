<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownFormatRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Symfony\Component\DomCrawler\Crawler;

class StatsDirectoryParser
{
	/** @var MonthDirectoryParser $monthDirectoryParser */
	protected $monthDirectoryParser;

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
	 * @param MonthDirectoryParser $monthDirectoryParser
	 * @param ShowdownFormatRepository $showdownFormatRepository
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param ShowdownAbilityRepository $showdownAbilityRepository
	 * @param ShowdownItemRepository $showdownItemRepository
	 * @param ShowdownNatureRepository $showdownNatureRepository
	 * @param ShowdownMoveRepository $showdownMoveRepository
	 */
	public function __construct(
		MonthDirectoryParser $monthDirectoryParser,
		ShowdownFormatRepository $showdownFormatRepository,
		ShowdownPokemonRepository $showdownPokemonRepository,
		ShowdownAbilityRepository $showdownAbilityRepository,
		ShowdownItemRepository $showdownItemRepository,
		ShowdownNatureRepository $showdownNatureRepository,
		ShowdownMoveRepository $showdownMoveRepository
	) {
		$this->monthDirectoryParser = $monthDirectoryParser;
		$this->showdownFormatRepository = $showdownFormatRepository;
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->showdownAbilityRepository = $showdownAbilityRepository;
		$this->showdownItemRepository = $showdownItemRepository;
		$this->showdownNatureRepository = $showdownNatureRepository;
		$this->showdownMoveRepository = $showdownMoveRepository;
	}

	/**
	 * Parse all month directories in this stats directory.
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

		// Get the HTML of the stats directory page.
		$html = $client->request('GET', $url)->getBody()->getContents();

		// Create the DOM crawler.
		$crawler = new Crawler($html, $url);

		// Get all the links on the stats directory page.
		$links = $crawler->filterXPath('//a[@href != "../"]')->links();

		// Import each month directory.
		foreach ($links as $link) {
			$this->monthDirectoryParser->parse($link->getUri());
		}
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
