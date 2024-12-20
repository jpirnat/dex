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
		$versionGroupModel = $this->statCalculatorIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$pokemons = $this->statCalculatorIndexModel->getPokemons();
		$pokemons = $this->dexFormatter->formatIvCalculatorPokemons($pokemons);

		$natures = $this->statCalculatorIndexModel->getNatures();
		$stats = $this->statCalculatorIndexModel->getStats();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Stat Calculator',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasNatures' => $versionGroup->hasNatures(),
					'statFormulaType' => $versionGroup->getStatFormulaType(),
					'maxIv' => $versionGroup->getMaxIv(),
					'maxEvsPerStat' => $versionGroup->getMaxEvsPerStat(),
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
