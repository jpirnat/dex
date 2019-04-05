<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsLeads\StatsLeadsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsLeadsController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsLeadsModel $statsLeadsModel */
	private $statsLeadsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsLeadsModel $statsLeadsModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsLeadsModel $statsLeadsModel
	) {
		$this->baseController = $baseController;
		$this->statsLeadsModel = $statsLeadsModel;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
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

		$this->statsLeadsModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
