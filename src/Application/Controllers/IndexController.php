<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Psr\Http\Message\ServerRequestInterface;

final readonly class IndexController
{
	public function __construct(
		private BaseController $baseController,
	) {}

	/**
	 * Show the home page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);
	}
}
