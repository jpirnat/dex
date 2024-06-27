<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexIndexModel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexIndexController
{
	public function __construct(
		private BaseController $baseController,
		private DexIndexModel $dexIndexModel,
	) {}

	/**
	 * Set data for the dex index page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');

		$this->dexIndexModel->setData($vgIdentifier);
	}
}
