<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ServerRequestInterface;

final class StatsIndexController
{
	private BaseController $baseController;
	private StatsIndexModel $statsIndexModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsIndexModel $statsIndexModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsIndexModel $statsIndexModel
	) {
		$this->baseController = $baseController;
		$this->statsIndexModel = $statsIndexModel;
	}

	/**
	 * Show the /stats page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$this->statsIndexModel->setMonths();
	}
}
