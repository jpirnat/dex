<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatCalculator\StatCalculatorIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatCalculatorIndexView
{
	public function __construct(
		private StatCalculatorIndexModel $statCalculatorIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the stat calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->statCalculatorIndexModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$pokemons = $this->statCalculatorIndexModel->pokemons;
		$pokemons = $this->dexFormatter->formatIvCalculatorPokemons($pokemons);

		$natures = $this->statCalculatorIndexModel->natures;
		$stats = $this->statCalculatorIndexModel->stats;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Stat Calculator',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasNatures' => $versionGroup->hasNatures,
					'statFormulaType' => $versionGroup->statFormulaType,
					'maxIv' => $versionGroup->maxIv,
					'maxEvsPerStat' => $versionGroup->maxEvsPerStat,
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
