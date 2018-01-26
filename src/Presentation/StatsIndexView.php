<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\StatsIndexModel;
use Jp\Dex\Domain\YearMonth;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class StatsIndexView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var StatsIndexModel $statsIndexModel */
	private $statsIndexModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param StatsIndexModel $statsIndexModel
	 */
	public function __construct(
		Twig_Environment $twig,
		StatsIndexModel $statsIndexModel
	) {
		$this->twig = $twig;
		$this->statsIndexModel = $statsIndexModel;
	}

	/**
	 * Show the /stats page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Get year/month combinations. Sort by year ascending, month ascending.
		$yearMonths = $this->statsIndexModel->getYearMonths();
		uasort(
			$yearMonths,
			function (YearMonth $a, YearMonth $b) : int {
				if ($a->getYear() === $b->getYear()) {
					return $a->getMonth() <=> $b->getMonth();
				}

				return $b->getYear() <=> $a->getYear();
			}
		);

		// Restructure the data for the template.
		$years = [];
		foreach ($yearMonths as $yearMonth) {
			$date = new DateTime();
			$date->setDate($yearMonth->getYear(), $yearMonth->getMonth(), 1);

			$years[$yearMonth->getYear()]['year'] = $yearMonth->getYear();
			$years[$yearMonth->getYear()]['months'][] = [
				'month' => $yearMonth->getMonth(),
				'name' => $date->format('M'),
			];
		}

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Stats',
			],
		];

		$content = $this->twig->render(
			'html/stats-index.twig',
			[
				'breadcrumbs' => $breadcrumbs,
				'years' => $years,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
