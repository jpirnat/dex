<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\EvCalculator\EvCalculatorIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class EvCalculatorIndexView
{
	public function __construct(
		private EvCalculatorIndexModel $evCalculatorIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the EV calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->evCalculatorIndexModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$pokemons = $this->evCalculatorIndexModel->pokemons;
		$pokemons = $this->dexFormatter->formatIvCalculatorPokemons($pokemons);

		$natures = $this->evCalculatorIndexModel->natures;
		$stats = $this->evCalculatorIndexModel->stats;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'EV Calculator',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'pokemons' => $pokemons,
				'natures' => $natures,
				'stats' => $stats,
			]
		]);
	}
}
