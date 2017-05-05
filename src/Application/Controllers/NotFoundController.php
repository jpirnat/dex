<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\NotFoundModel;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundController
{
	/** @var NotFoundModel $notFoundModel */
	private $notFoundModel;

	/**
	 * Constructor.
	 *
	 * @param NotFoundModel $notFoundModel
	 */
	public function __construct(
		NotFoundModel $notFoundModel
	) {
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
	}
}
