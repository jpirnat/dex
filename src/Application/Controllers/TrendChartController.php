<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\TrendChartModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class TrendChartController
{
	private TrendChartModel $trendChartModel;

	/**
	 * Constructor.
	 *
	 * @param TrendChartModel $trendChartModel
	 */
	public function __construct(
		TrendChartModel $trendChartModel
	) {
		$this->trendChartModel = $trendChartModel;
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

		$this->trendChartModel->setData($lines, $languageId);
	}
}
