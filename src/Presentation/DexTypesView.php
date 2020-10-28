<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexTypesView
{
	public function __construct(
		private DexTypesModel $dexTypesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexTypesModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$types = $this->dexTypesModel->getTypes();
		$multipliers = $this->dexTypesModel->getMultipliers();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Types',
		]];

		return new JsonResponse([
			'data' => [
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),

				'types' => $this->dexFormatter->formatDexTypes($types),
				'multipliers' => $multipliers,
			]
		]);
	}
}
