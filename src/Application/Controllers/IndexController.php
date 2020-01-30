<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Psr\Http\Message\ServerRequestInterface;

final class IndexController
{
	private BaseController $baseController;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 */
	public function __construct(BaseController $baseController)
	{
		$this->baseController = $baseController;
	}

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
