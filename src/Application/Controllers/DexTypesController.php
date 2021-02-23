<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexTypesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexTypesController
{
	public function __construct(
		private BaseController $baseController,
		private DexTypesModel $dexTypesModel,
	) {
	}

	/**
	 * Show the dex types page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexTypesModel->setData($generationIdentifier, $languageId);
	}
}
