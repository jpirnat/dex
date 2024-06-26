<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexMoveController
{
	public function __construct(
		private BaseController $baseController,
		private DexMoveModel $dexMoveModel,
	) {}

	/**
	 * Set data for the dex move page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$moveIdentifier = $request->getAttribute('moveIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexMoveModel->setData($vgIdentifier, $moveIdentifier, $languageId);
	}
}
