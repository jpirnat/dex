<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemonsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexPokemonsView
{
	private DexPokemonsModel $dexPokemonsModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param DexPokemonsModel $dexPokemonsModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		DexPokemonsModel $dexPokemonsModel,
		DexFormatter $dexFormatter
	) {
		$this->dexPokemonsModel = $dexPokemonsModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex Pokémons page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexPokemonsModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$showAbilities = $generation->getId()->value() >= 3;
		$stats = $this->dexPokemonsModel->getStats();
		$pokemon = $this->dexPokemonsModel->getPokemon();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Pokémon',
		]];

		return new JsonResponse([
			'data' => [
				'breadcrumbs' => $breadcrumbs,
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'showAbilities' => $showAbilities,
				'stats' => $stats,
				'pokemons' => $this->dexFormatter->formatDexPokemon($pokemon),
			]
		]);

		return new HtmlResponse($content);
	}
}
