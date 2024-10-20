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
		$versionGroupModel = $this->ivCalculatorIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$pokemons = $this->ivCalculatorIndexModel->getPokemons();
		$pokemons = $this->dexFormatter->formatIvCalculatorPokemons($pokemons);

		$natures = $this->ivCalculatorIndexModel->getNatures();
		$characteristics = $this->ivCalculatorIndexModel->getCharacteristics();
		$types = $this->ivCalculatorIndexModel->getTypes();
		$stats = $this->ivCalculatorIndexModel->getStats();

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
