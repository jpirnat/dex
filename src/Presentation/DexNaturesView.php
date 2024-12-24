<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexNaturesView
{
	public function __construct(
		private DexNaturesModel $dexNaturesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex natures page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexNaturesModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$natures = $this->dexNaturesModel->natures;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Natures',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'natures' => $natures,
			]
		]);
	}
}
