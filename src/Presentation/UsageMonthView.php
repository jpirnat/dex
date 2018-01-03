<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Application\Models\UsageMonth\UsageData;
use Jp\Dex\Application\Models\UsageMonth\UsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class UsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var UsageMonthModel $usageMonthModel */
	private $usageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param DateModel $dateModel
	 * @param UsageMonthModel $usageMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		DateModel $dateModel,
		UsageMonthModel $usageMonthModel
	) {
		$this->twig = $twig;
		$this->dateModel = $dateModel;
		$this->usageMonthModel = $usageMonthModel;
	}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		// Get the previous month and the next month.
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get usage data and sort by rank.
		$usageDatas = $this->usageMonthModel->getUsageDatas();
		uasort(
			$usageDatas,
			function (UsageData $a, UsageData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($usageDatas as $usageData) {
			$data[] = [
				'rank' => $usageData->getRank(),
				'name' => $usageData->getPokemonName(),
				'showMovesetLink' => $usageData->getUsagePercent() >= .01,
				'identifier' => $usageData->getPokemonIdentifier(),
				'formIcon' => $usageData->getFormIcon(),
				'usagePercent' => $usageData->getUsagePercent(),
				'usageChange' => $usageData->getUsageChange(),
				'raw' => $usageData->getRaw(),
				'rawPercent' => $usageData->getRawPercent(),
				'rawChange' => $usageData->getRawChange(),
				'real' => $usageData->getReal(),
				'realPercent' => $usageData->getRealPercent(),
				'realChange' => $usageData->getRealChange(),
			];
		}

		$content = $this->twig->render(
			'html/usage-month.twig',
			[
				// The month control's data.
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'formatIdentifier' => $this->usageMonthModel->getFormatIdentifier(),
				'rating' => $this->usageMonthModel->getRating(),

				'year' => $this->usageMonthModel->getYear(),
				'month' => $this->usageMonthModel->getMonth(),

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
