<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsIndexController
{
	public function __construct(
		private BaseController $baseController,
		private StatsIndexModel $statsIndexModel,
	) {}

	/**
	 * Set data for the stats index page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$this->statsIndexModel->setData();
	}
}
