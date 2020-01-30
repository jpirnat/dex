<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\NotFoundModel;
use Psr\Http\Message\ServerRequestInterface;

final class NotFoundController
{
	private BaseController $baseController;
	private NotFoundModel $notFoundModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param NotFoundModel $notFoundModel
	 */
	public function __construct(
		BaseController $baseController,
		NotFoundModel $notFoundModel
	) {
		$this->baseController = $baseController;
		$this->notFoundModel = $notFoundModel;
	}

	/**
	 * Get the 404 Not Found page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function get404(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);
	}
}
