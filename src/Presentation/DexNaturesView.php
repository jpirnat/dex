<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexNaturesView
{
	public function __construct(
		private DexNaturesModel $dexNaturesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex natures page.
	 */
	public function index() : ResponseInterface
	{
		$versionGroupModel = $this->dexNaturesModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$natures = $this->dexNaturesModel->getNatures();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Natures',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'natures' => $natures,
			]
		]);
	}
}
