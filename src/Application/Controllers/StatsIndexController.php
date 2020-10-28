<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ServerRequestInterface;

final class StatsIndexController
{
	public function __construct(
		private BaseController $baseController,
		private StatsIndexModel $statsIndexModel,
	) {}

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
