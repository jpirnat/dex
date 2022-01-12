<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsAveragedLeads\StatsAveragedLeadsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsAveragedLeadsController
{
	public function __construct(
		private BaseController $baseController,
		private StatsAveragedLeadsModel $statsAveragedLeadsModel,
	) {}

	/**
	 * Get leads data averaged over multiple months.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsAveragedLeadsModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
