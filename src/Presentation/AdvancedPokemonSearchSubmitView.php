<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AdvancedPokemonSearch\AdvancedPokemonSearchSubmitModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AdvancedPokemonSearchSubmitView
{
	public function __construct(
		private AdvancedPokemonSearchSubmitModel $advancedPokemonSearchSubmitModel,
		private DexFormatter $dexFormatter
	) {}

	/**
	 * Get data for the advanced Pokémon search page.
	 */
	public function getData() : ResponseInterface
	{
		$pokemons = $this->advancedPokemonSearchSubmitModel->getPokemons();
		$pokemons = $this->dexFormatter->formatDexPokemon($pokemons);

		return new JsonResponse([
			'data' => [
				'pokemons' => $pokemons,
			]
		]);
	}
}
