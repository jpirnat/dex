<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMovesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexMovesController
{
	public function __construct(
		private BaseController $baseController,
		private DexMovesModel $dexMovesModel,
	) {}

	/**
	 * Show the dex moves page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexMovesModel->setData($vgIdentifier, $languageId);
	}
}
