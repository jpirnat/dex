<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\LeadsMonth\LeadsMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class LeadsMonthController
{
	/** @var LeadsMonthModel $leadsMonthModel */
	private $leadsMonthModel;

	/**
	 * Constructor.
	 *
	 * @param LeadsMonthModel $leadsMonthModel
	 */
	public function __construct(LeadsMonthModel $leadsMonthModel)
	{
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
		$year = (int) $request->getAttribute('year');
		$month = (int) $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->leadsMonthModel->setData(
			$year,
			$month,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
