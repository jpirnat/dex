<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\EvCalculator\EvCalculatorSubmitModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class EvCalculatorSubmitView
{
	public function __construct(
		private EvCalculatorSubmitModel $evCalculatorSubmitModel,
	) {}

	/**
	 * Get data for the EV calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$evs = $this->evCalculatorSubmitModel->getEvs();

		return new JsonResponse([
			'data' => [
				'evs' => $evs,
			]
		]);
	}
}
