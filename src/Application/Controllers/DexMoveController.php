<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexMoveController
{
	public function __construct(
		private BaseController $baseController,
		private DexMoveModel $dexMoveModel,
	) {}

	/**
	 * Show the dex move page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$moveIdentifier = $request->getAttribute('moveIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexMoveModel->setData($generationIdentifier, $moveIdentifier, $languageId);
	}
}
