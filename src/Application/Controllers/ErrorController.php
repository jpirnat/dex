<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\ErrorModel;
use Psr\Http\Message\ServerRequestInterface;

class ErrorController
{
	/** @var ErrorModel $errorModel */
	protected $errorModel;

	/**
	 * Constructor.
	 *
	 * @param ErrorModel $errorModel
	 */
	public function __construct(
		ErrorModel $errorModel
	) {
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
	}
}
