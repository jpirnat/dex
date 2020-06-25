<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexNaturesView
{
	private DexNaturesModel $dexNaturesModel;

	/**
	 * Constructor.
	 *
	 * @param DexNaturesModel $dexNaturesModel
	 */
	public function __construct(
		DexNaturesModel $dexNaturesModel
	) {
		$this->dexNaturesModel = $dexNaturesModel;
	}

	/**
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$natures = $this->dexNaturesModel->getNatures();

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Natures',
			],
		];

		return new JsonResponse([
			'data' => [
				'breadcrumbs' => $breadcrumbs,
				'natures' => $natures,
			]
		]);
	}
}
