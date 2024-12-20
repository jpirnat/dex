<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class IvCalculatorIndexView
{
	public function __construct(
		private IvCalculatorIndexModel $ivCalculatorIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the IV calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->ivCalculatorIndexModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$pokemons = $this->ivCalculatorIndexModel->pokemons;
		$pokemons = $this->dexFormatter->formatIvCalculatorPokemons($pokemons);

		$natures = $this->ivCalculatorIndexModel->natures;
		$characteristics = $this->ivCalculatorIndexModel->characteristics;
		$types = $this->ivCalculatorIndexModel->types;
		$stats = $this->ivCalculatorIndexModel->stats;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'IV Calculator',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'maxEvsPerStat' => $versionGroup->getMaxEvsPerStat(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'pokemons' => $pokemons,
				'natures' => $natures,
				'characteristics' => $characteristics,
				'types' => $types,
				'stats' => $stats,
			]
		]);
	}
}
