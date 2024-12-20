<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\StatCalculator\StatCalculatorSubmitModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class StatCalculatorSubmitView
{
	public function __construct(
		private StatCalculatorSubmitModel $statCalculatorSubmitModel,
	) {}

	/**
	 * Get data for the stat calculator page.
	 */
	public function getData() : ResponseInterface
	{
		$finalStats = $this->statCalculatorSubmitModel->finalStats;
		$cp = $this->statCalculatorSubmitModel->cp;

		return new JsonResponse([
			'data' => [
				'finalStats' => $finalStats,
				'cp' => $cp,
			]
		]);
	}
}
