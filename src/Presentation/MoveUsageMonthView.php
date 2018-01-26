<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageData;
use Jp\Dex\Application\Models\MoveUsageMonth\MoveUsageMonthModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class MoveUsageMonthView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var MoveUsageMonthModel $moveUsageMonthModel */
	private $moveUsageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param MoveUsageMonthModel $moveUsageMonthModel
	 */
	public function __construct(
		Twig_Environment $twig,
		MoveUsageMonthModel $moveUsageMonthModel
	) {
		$this->twig = $twig;
		$this->moveUsageMonthModel = $moveUsageMonthModel;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific move.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$year = $this->moveUsageMonthModel->getYear();
		$month = $this->moveUsageMonthModel->getMonth();
		$formatIdentifier = $this->moveUsageMonthModel->getFormatIdentifier();
		$rating = $this->moveUsageMonthModel->getRating();

		// Get the previous month and the next month.
		$prevMonth = $this->moveUsageMonthModel->getDateModel()->getPrevMonth();
		$nextMonth = $this->moveUsageMonthModel->getDateModel()->getNextMonth();

		// Get move usage data and sort by usage percent.
		$moveUsageDatas = $this->moveUsageMonthModel->getMoveUsageDatas();
		uasort(
			$moveUsageDatas,
			function (MoveUsageData $a, MoveUsageData $b) : int {
				return $b->getUsagePercent() <=> $a->getUsagePercent();
			}
		);

		// Compile all move usage data into the right form.
		$data = [];
		foreach ($moveUsageDatas as $moveUsageData) {
			$data[] = [
				'name' => $moveUsageData->getPokemonName(),
				'identifier' => $moveUsageData->getPokemonIdentifier(),
				'formIcon' => $moveUsageData->getFormIcon(),
				'pokemonPercent' => $moveUsageData->getPokemonPercent(),
				'movePercent' => $moveUsageData->getMovePercent(),
				'usagePercent' => $moveUsageData->getUsagePercent(),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'url' => "/stats/$year/$month",
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$year/$month/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => $this->moveUsageMonthModel->getMoveName()->getName(),
			],
		];

		$content = $this->twig->render(
			'html/move-usage-month.twig',
			[
				'breadcrumbs' => $breadcrumbs,

				// The month control's data.
				'showPrevMonthLink' => $this->moveUsageMonthModel->doesPrevMonthDataExist(),
				'prevYear' => $prevMonth->getYear(),
				'prevMonth' => $prevMonth->getMonth(),
				'showNextMonthLink' => $this->moveUsageMonthModel->doesNextMonthDataExist(),
				'nextYear' => $nextMonth->getYear(),
				'nextMonth' => $nextMonth->getMonth(),
				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,
				'moveIdentifier' => $this->moveUsageMonthModel->getMoveIdentifier(),

				'year' => $year,
				'month' => $month,

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
