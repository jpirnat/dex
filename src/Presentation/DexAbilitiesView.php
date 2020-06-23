<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexAbilitiesView
{
	private DexAbilitiesModel $dexAbilitiesModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param DexAbilitiesModel $dexAbilitiesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		DexAbilitiesModel $dexAbilitiesModel,
		DexFormatter $dexFormatter
	) {
		$this->dexAbilitiesModel = $dexAbilitiesModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexAbilitiesModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$abilities = $this->dexAbilitiesModel->getAbilities();

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Abilities',
			],
		];

		return new JsonResponse([
			'data' => [
				'breadcrumbs' => $breadcrumbs,
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'abilities' => $abilities,
			]
		]);
	}
}
