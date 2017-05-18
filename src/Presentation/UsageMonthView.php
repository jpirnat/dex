<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\UsageMonthModel;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class UsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var UsageMonthModel $usageMonthModel */
	private $usageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param UsageMonthModel $usageMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		UsageMonthModel $usageMonthModel
	) {
		$this->twig = $twig;
		$this->usageMonthModel = $usageMonthModel;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$usagePokemons = $this->usageMonthModel->getUsagePokemon();
		$usageRatedPokemons = $this->usageMonthModel->getUsageRatedPokemon();
		$pokemonNames = $this->usageMonthModel->getPokemonNames();

		// Sort usage rated Pokémon records by rank ascending.
		uasort(
			$usageRatedPokemons,
			function (UsageRatedPokemon $a, UsageRatedPokemon $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		$data = [];

		foreach ($usageRatedPokemons as $pokemonIdValue => $usageRatedPokemon) {
			$pokemonName = $pokemonNames[$pokemonIdValue];
			$usagePokemon = $usagePokemons[$pokemonIdValue];
			// TODO: Error handling if the key does not exist?

			$data[] = [
				'rank' => $usageRatedPokemon->getRank(),
				'pokemonId' => $pokemonIdValue,
				'name' => $pokemonName->getName(),
				'usagePercent' => $usageRatedPokemon->getUsagePercent(),
				'raw' => $usagePokemon->getRaw(),
				'rawPercent' => $usagePokemon->getRawPercent(),
				'real' => $usagePokemon->getReal(),
				'realPercent' => $usagePokemon->getRealPercent(),
			];
		}

		$content = $this->twig->render(
			'usage-month.twig',
			[
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);

		return $response;
	}
}
