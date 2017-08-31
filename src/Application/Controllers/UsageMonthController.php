<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Application\Models\UsageMonth\UsageMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class UsageMonthController
{
	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var UsageMonthModel $usageMonthModel */
	private $usageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param DateModel $dateModel
	 * @param UsageMonthModel $usageMonthModel
	 */
	public function __construct(
		DateModel $dateModel,
		UsageMonthModel $usageMonthModel
	) {
		$this->dateModel = $dateModel;
		$this->usageMonthModel = $usageMonthModel;
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
		$year = (int) $request->getAttribute('year');
		$month = (int) $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dateModel->setData($year, $month);

		$this->usageMonthModel->setData(
			$year,
			$month,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
