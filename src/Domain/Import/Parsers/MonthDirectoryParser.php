<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Extractors\FormatRatingExtractor;
use Jp\Dex\Domain\Import\Extractors\MonthExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class MonthDirectoryParser
{
	/** @var UsageFileParser $usageFileParser */
	private $usageFileParser;

	/** @var LeadsDirectoryParser $leadsDirectoryParser */
	private $leadsDirectoryParser;

	/** @var MovesetDirectoryParser $movesetDirectoryParser */
	private $movesetDirectoryParser;

	/** @var MonthExtractor $monthExtractor */
	private $monthExtractor;

	/** @var FormatRatingExtractor $formatRatingExtractor */
	private $formatRatingExtractor;

	/** @var ShowdownFormatRepositoryInterface $showdownFormatRepository */
	private $showdownFormatRepository;

	/** @var ShowdownPokemonRepositoryInterface $showdownPokemonRepository */
	private $showdownPokemonRepository;

	/** @var ShowdownAbilityRepositoryInterface $showdownAbilityRepository */
	private $showdownAbilityRepository;

	/** @var ShowdownItemRepositoryInterface $showdownItemRepository */
	private $showdownItemRepository;

	/** @var ShowdownNatureRepositoryInterface $showdownNatureRepository */
	private $showdownNatureRepository;

	/** @var ShowdownMoveRepositoryInterface $showdownMoveRepository */
	private $showdownMoveRepository;

	/**
	 * Constructor.
	 *
	 * @param UsageFileParser $usageFileParser
	 * @param LeadsDirectoryParser $leadsDirectoryParser
	 * @param MovesetDirectoryParser $movesetDirectoryParser
	 * @param MonthExtractor $monthExtractor
	 * @param FormatRatingExtractor $formatRatingExtractor
	 * @param ShowdownFormatRepositoryInterface $showdownFormatRepository
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param ShowdownAbilityRepositoryInterface $showdownAbilityRepository
	 * @param ShowdownItemRepositoryInterface $showdownItemRepository
	 * @param ShowdownNatureRepositoryInterface $showdownNatureRepository
	 * @param ShowdownMoveRepositoryInterface $showdownMoveRepository
	 */
	public function __construct(
		UsageFileParser $usageFileParser,
		LeadsDirectoryParser $leadsDirectoryParser,
		MovesetDirectoryParser $movesetDirectoryParser,
		MonthExtractor $monthExtractor,
		FormatRatingExtractor $formatRatingExtractor,
		ShowdownFormatRepositoryInterface $showdownFormatRepository,
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		ShowdownItemRepositoryInterface $showdownItemRepository,
		ShowdownNatureRepositoryInterface $showdownNatureRepository,
		ShowdownMoveRepositoryInterface $showdownMoveRepository
	) {
		$this->usageFileParser = $usageFileParser;
		$this->leadsDirectoryParser = $leadsDirectoryParser;
		$this->movesetDirectoryParser = $movesetDirectoryParser;
		$this->monthExtractor = $monthExtractor;
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

		// Get the month from the month directory url.
		$month = $this->monthExtractor->extractMonth($url);

		// Parse each usage file link.
		foreach ($links as $link) {
			// Get the format and rating from the filename of the link.
			$filename = pathinfo($link->getUri())['filename'];
			$formatRating = $this->formatRatingExtractor->extractFormatRating($filename);
			$showdownFormatName = $formatRating->showdownFormatName();

			// If the format is unknown, add it to the list of unknown formats.
			$formatUnknown = !$this->showdownFormatRepository->isKnown(
				$month,
				$showdownFormatName
			);
			if ($formatUnknown) {
				$this->showdownFormatRepository->addUnknown($month, $showdownFormatName);
			}

			// Create a stream to read the usage file.
			$stream = $client->request('GET', $link->getUri())->getBody();

			// Parse the usage file.
			$totalBattles = $this->usageFileParser->parse($stream);

			// Keep track of which new formats should be ignored because they
			// don't have enough battles this month.
			$tooFewBattles = 0 <= $totalBattles && $totalBattles <= 100;
			if ($formatUnknown && $tooFewBattles) {
				$yearMonth = $month->format('Y-m');
				$format = $formatRating->showdownFormatName();
				$rating = $formatRating->rating();
				echo "$yearMonth\t$format\t$rating\ttoo few battles: $totalBattles\n";
			}
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
