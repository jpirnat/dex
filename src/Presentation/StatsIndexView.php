<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTime;
use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class StatsIndexView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var StatsIndexModel $statsIndexModel */
	private $statsIndexModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param StatsIndexModel $statsIndexModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		StatsIndexModel $statsIndexModel
	) {
		$this->renderer = $renderer;
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
				'month' => $month->format('Y-m'),
				'name' => $month->format('M'),
			];
		}

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Stats',
			],
		];

		$content = $this->renderer->render(
			'html/stats/index.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Competitive Pokémon Stats Archive',
				'breadcrumbs' => $breadcrumbs,
				'years' => $years,
			]
		);

		return new HtmlResponse($content);
	}
}
