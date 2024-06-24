<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexTypesView
{
	public function __construct(
		private DexTypesModel $dexTypesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex types page.
	 */
	public function index() : ResponseInterface
	{
		$versionGroupModel = $this->dexTypesModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

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
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'types' => $types,
				'multipliers' => $multipliers,
			]
		]);
	}
}
