<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsIndexModel;
use Psr\Http\Message\ServerRequestInterface;

class StatsIndexController
{
	/** @var StatsIndexModel $statsIndexModel */
	private $statsIndexModel;

	/**
	 * Constructor.
	 *
	 * @param StatsIndexModel $statsIndexModel
	 */
	public function __construct(StatsIndexModel $statsIndexModel)
	{
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
		$this->statsIndexModel->setYearMonths();
	}
}
