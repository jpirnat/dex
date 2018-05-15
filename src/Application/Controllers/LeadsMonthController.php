<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\LeadsMonth\LeadsMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class LeadsMonthController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var LeadsMonthModel $leadsMonthModel */
	private $leadsMonthModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param LeadsMonthModel $leadsMonthModel
	 */
	public function __construct(
		BaseController $baseController,
		LeadsMonthModel $leadsMonthModel
	) {
		$this->baseController = $baseController;
		$this->leadsMonthModel = $leadsMonthModel;
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

		$this->leadsMonthModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
