<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DateModel;
use Jp\Dex\Application\Models\LeadsMonth\LeadsData;
use Jp\Dex\Application\Models\LeadsMonth\LeadsMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class LeadsMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var DateModel $dateModel */
	private $dateModel;

	/** @var LeadsMonthModel $leadsMonthModel */
	private $leadsMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param DateModel $dateModel
	 * @param LeadsMonthModel $leadsMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		DateModel $dateModel,
		LeadsMonthModel $leadsMonthModel
	) {
		$this->twig = $twig;
		$this->dateModel = $dateModel;
		$this->leadsMonthModel = $leadsMonthModel;
	}

	/**
	 * Get leads data to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		// Get the previous month and the next month.
		$prevMonth = $this->dateModel->getPrevMonth();
		$nextMonth = $this->dateModel->getNextMonth();

		// Get usage data and sort by rank.
		$leadsDatas = $this->leadsMonthModel->getLeadsDatas();
		uasort(
			$leadsDatas,
			function (LeadsData $a, LeadsData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($leadsDatas as $leadsData) {
			$data[] = [
				'rank' => $leadsData->getRank(),
				'name' => $leadsData->getPokemonName(),
				'showMovesetLink' => $leadsData->getUsagePercent() >= .01,
				'identifier' => $leadsData->getPokemonIdentifier(),
				'formIcon' => $leadsData->getFormIcon(),
				'usagePercent' => $leadsData->getLeadUsagePercent(),
				'usageChange' => $leadsData->getLeadUsageChange(),
				'raw' => $leadsData->getRaw(),
				'rawPercent' => $leadsData->getRawPercent(),
				'rawChange' => $leadsData->getRawChange(),
			];
		}

		$content = $this->twig->render(
			'html/leads-month.twig',
			[
				// The month control's data.
				'showPrevMonthLink' => true,
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'showNextMonthLink' => true,
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'formatIdentifier' => $this->leadsMonthModel->getFormatIdentifier(),
				'rating' => $this->leadsMonthModel->getRating(),

				'year' => $this->leadsMonthModel->getYear(),
				'month' => $this->leadsMonthModel->getMonth(),

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
