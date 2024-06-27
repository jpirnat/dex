<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemonsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexPokemonsView
{
	public function __construct(
		private DexPokemonsModel $dexPokemonsModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex Pokémons page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexPokemonsModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$showAbilities = $versionGroup->getId()->hasAbilities();
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
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'pokemons' => $this->dexFormatter->formatDexPokemon($pokemon),
				'showAbilities' => $showAbilities,
				'stats' => $stats,
			]
		]);
	}
}
