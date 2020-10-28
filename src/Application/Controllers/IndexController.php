<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Psr\Http\Message\ServerRequestInterface;

final class IndexController
{
	public function __construct(
		private BaseController $baseController,
	) {}

	/**
	 * Show the home page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);
	}
}
