<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsUsage\StatsUsageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsUsageController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsUsageModel $statsUsageModel */
	private $statsUsageModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsUsageModel $statsUsageModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsUsageModel $statsUsageModel
	) {
		$this->baseController = $baseController;
		$this->statsUsageModel = $statsUsageModel;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsUsageModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
