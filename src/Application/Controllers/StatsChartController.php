<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsChartModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsChartController
{
	private StatsChartModel $statsChartModel;

	/**
	 * Constructor.
	 *
	 * @param StatsChartModel $statsChartModel
	 */
	public function __construct(
		StatsChartModel $statsChartModel
	) {
		$this->statsChartModel = $statsChartModel;
	}

	/**
	 * Set data for the /stats/chart page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function ajax(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$lines = $data['lines'] ?? [];

		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsChartModel->setData($lines, $languageId);
	}
}
