<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\ChartsModel;
use Psr\Http\Message\ServerRequestInterface;

class ChartsController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var ChartsModel $chartsModel */
	private $chartsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param ChartsModel $chartsModel
	 */
	public function __construct(
		BaseController $baseController,
		ChartsModel $chartsModel
	) {
		$this->baseController = $baseController;
		$this->chartsModel = $chartsModel;
	}

	/**
	 * Show the /stats/charts page.
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
	 * Set data for the /stats/charts page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function ajax(ServerRequestInterface $request) : void
	{
		$lines = $request->getParsedBody()['lines'] ?? [];		

		$this->chartsModel->setData($lines);
	}
}
