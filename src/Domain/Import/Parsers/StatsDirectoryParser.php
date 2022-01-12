<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Parsers;

use GuzzleHttp\Client;
use Jp\Dex\Domain\Import\Showdown\ShowdownAbilityRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownFormatRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownMoveRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownNatureRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Symfony\Component\DomCrawler\Crawler;

final class StatsDirectoryParser
{
	public function __construct(
		private MonthDirectoryParser $monthDirectoryParser,
		private ShowdownFormatRepositoryInterface $showdownFormatRepository,
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private ShowdownAbilityRepositoryInterface $showdownAbilityRepository,
		private ShowdownItemRepositoryInterface $showdownItemRepository,
		private ShowdownNatureRepositoryInterface $showdownNatureRepository,
		private ShowdownMoveRepositoryInterface $showdownMoveRepository,
	) {}

	/**
	 * Parse all month directories in this stats directory.
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
	 * @return string[][]
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
