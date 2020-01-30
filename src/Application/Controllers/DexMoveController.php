<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexMoveController
{
	private BaseController $baseController;
	private DexMoveModel $dexMoveModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexMoveModel $dexMoveModel
	 */
	public function __construct(
		BaseController $baseController,
		DexMoveModel $dexMoveModel
	) {
		$this->baseController = $baseController;
		$this->dexMoveModel = $dexMoveModel;
	}

	/**
	 * Show the dex move page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
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
