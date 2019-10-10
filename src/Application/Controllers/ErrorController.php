<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\ErrorModel;
use Psr\Http\Message\ServerRequestInterface;

final class ErrorController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var ErrorModel $errorModel */
	private $errorModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param ErrorModel $errorModel
	 */
	public function __construct(
		BaseController $baseController,
		ErrorModel $errorModel
	) {
		$this->baseController = $baseController;
		$this->errorModel = $errorModel;
	}

	/**
	 * Get the Error page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function getError(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);
	}
}
