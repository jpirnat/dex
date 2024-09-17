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
		$versionGroupModel = $this->evCalculatorIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$pokemons = $this->evCalculatorIndexModel->getPokemons();
		$natures = $this->evCalculatorIndexModel->getNatures();
		$stats = $this->evCalculatorIndexModel->getStats();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'EV Calculator',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
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
