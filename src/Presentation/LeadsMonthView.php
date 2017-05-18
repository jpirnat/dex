<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\LeadsMonthModel;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class LeadsMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var LeadsMonthModel $leadsMonthModel */
	private $leadsMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param LeadsMonthModel $leadsMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		LeadsMonthModel $leadsMonthModel
	) {
		$this->twig = $twig;
		$this->leadsMonthModel = $leadsMonthModel;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$leadsPokemons = $this->leadsMonthModel->getLeadsPokemon();
		$leadsRatedPokemons = $this->leadsMonthModel->getLeadsRatedPokemon();
		$pokemons = $this->leadsMonthModel->getPokemon();
		$pokemonNames = $this->leadsMonthModel->getPokemonNames();

		// Sort leads rated Pokémon records by rank ascending.
		uasort(
			$leadsRatedPokemons,
			function (LeadsRatedPokemon $a, LeadsRatedPokemon $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		$data = [];

		foreach ($leadsRatedPokemons as $pokemonIdValue => $leadsRatedPokemon) {
			$pokemon = $pokemons[$pokemonIdValue];
			$pokemonName = $pokemonNames[$pokemonIdValue];
			$leadsPokemon = $leadsPokemons[$pokemonIdValue];
			// TODO: Error handling if the key does not exist?

			$data[] = [
				'rank' => $leadsRatedPokemon->getRank(),
				'id' => $pokemonIdValue,
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemonName->getName(),
				'usagePercent' => $leadsRatedPokemon->getUsagePercent(),
				'raw' => $leadsPokemon->getRaw(),
				'rawPercent' => $leadsPokemon->getRawPercent(),
			];
		}

		$content = $this->twig->render(
			'leads-month.twig',
			[
				'year' => $this->leadsMonthModel->getYear(),
				'month' => $this->leadsMonthModel->getMonth(),
				'formatIdentifier' => $this->leadsMonthModel->getFormatIdentifier(),
				'rating' => $this->leadsMonthModel->getRating(),
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
