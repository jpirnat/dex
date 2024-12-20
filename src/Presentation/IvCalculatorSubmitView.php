<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorSubmitModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class IvCalculatorSubmitView
{
	public function __construct(
		private IvCalculatorSubmitModel $ivCalculatorSubmitModel,
	) {}

	/**
	 * Get data for the IV calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$ivs = $this->ivCalculatorSubmitModel->ivs;

		return new JsonResponse([
			'data' => [
				'ivs' => $ivs,
			]
		]);
	}
}
