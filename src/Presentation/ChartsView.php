<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ChartsModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

class ChartsView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var ChartsModel $chartsModel */
	private $chartsModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param ChartsModel $chartsModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		ChartsModel $chartsModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->chartsModel = $chartsModel;
	}

	/**
	 * Show the /stats/charts page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'text' => 'Charts',
			]
		];

		$content = $this->twig->render(
			'html/charts.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Porydex - Stats - Saved Charts',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}

	/**
	 * Set data for the /stats/charts page.
	 *
	 * @return ResponseInterface
	 */
	public function ajax() : ResponseInterface
	{
		return new JsonResponse([]);
	}
}
