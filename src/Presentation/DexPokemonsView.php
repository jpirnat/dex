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
		$versionGroupModel = $this->dexPokemonsModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$stats = $this->dexPokemonsModel->stats;
		$pokemon = $this->dexPokemonsModel->pokemon;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Pokémon',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'pokemons' => $this->dexFormatter->formatDexPokemon($pokemon),
				'stats' => $stats,
			]
		]);
	}
}
