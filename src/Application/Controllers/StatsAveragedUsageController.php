<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsAveragedUsage\StatsAveragedUsageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsAveragedUsageController
{
	private BaseController $baseController;
	private StatsAveragedUsageModel $usageAveragedModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsAveragedUsageModel $usageAveragedModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsAveragedUsageModel $usageAveragedModel
	) {
		$this->baseController = $baseController;
		$this->usageAveragedModel = $usageAveragedModel;
	}

	/**
	 * Get usage data averaged over multiple months.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->usageAveragedModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
