<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsMonth\StatsMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsMonthController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsMonthModel $statsMonthModel */
	private $statsMonthModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsMonthModel $statsMonthModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsMonthModel $statsMonthModel
	) {
		$this->baseController = $baseController;
		$this->statsMonthModel = $statsMonthModel;
	}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsMonthModel->setData(
			$month,
			$languageId
		);
	}
}
