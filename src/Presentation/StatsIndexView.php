<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class StatsIndexView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsIndexModel $statsIndexModel */
	private $statsIndexModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param StatsIndexModel $statsIndexModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		StatsIndexModel $statsIndexModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->statsIndexModel = $statsIndexModel;
	}

	/**
	 * Show the /stats page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Get months. Sort by year ascending, month ascending.
		$months = $this->statsIndexModel->getMonths();
		uasort(
			$months,
			function (DateTime $a, DateTime $b) : int {
				if ($a->format('Y') === $b->format('Y')) {
					return $a->format('n') <=> $b->format('n');
				}

				return $b->format('Y') <=> $a->format('Y');
			}
		);

		// Restructure the data for the template.
		$years = [];
		foreach ($months as $month) {
			$year = (int) $month->format('Y');

			$years[$year]['year'] = $year;
			$years[$year]['months'][] = [
				'month' => (int) $month->format('n'),
				'name' => $month->format('M'),
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
			$this->baseView->getBaseVariables() + [
				'title' => 'Competitive Pokémon Stats Archive',
				'breadcrumbs' => $breadcrumbs,
				'years' => $years,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
