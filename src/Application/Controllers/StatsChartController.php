<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsChartModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsChartController
{
	public function __construct(
		private StatsChartModel $statsChartModel,
	) {}

	/**
	 * Set data for the stats chart page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$lines = $data['lines'] ?? [];

		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsChartModel->setData($lines, $languageId);
	}
}
