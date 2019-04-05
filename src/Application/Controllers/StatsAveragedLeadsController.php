<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsAveragedLeads\StatsAveragedLeadsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsAveragedLeadsController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsAveragedLeadsModel $statsAveragedLeadsModel */
	private $statsAveragedLeadsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsAveragedLeadsModel $statsAveragedLeadsModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsAveragedLeadsModel $statsAveragedLeadsModel
	) {
		$this->baseController = $baseController;
		$this->statsAveragedLeadsModel = $statsAveragedLeadsModel;
	}

	/**
	 * Get leads data averaged over multiple months.
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

		$this->statsAveragedLeadsModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
