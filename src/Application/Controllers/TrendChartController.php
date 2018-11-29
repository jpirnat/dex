<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\TrendChartModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class TrendChartController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var TrendChartModel $trendChartModel */
	private $trendChartModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param TrendChartModel $trendChartModel
	 */
	public function __construct(
		BaseController $baseController,
		TrendChartModel $trendChartModel
	) {
		$this->baseController = $baseController;
		$this->trendChartModel = $trendChartModel;
	}

	/**
	 * Show the /stats/trends/chart page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);
	}

	/**
	 * Set data for the /stats/trends/chart page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function ajax(ServerRequestInterface $request) : void
	{
		$lines = $request->getParsedBody()['lines'] ?? [];

		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->trendChartModel->setData($lines, $languageId);
	}
}
