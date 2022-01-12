<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexAbilitiesController
{
	public function __construct(
		private BaseController $baseController,
		private DexAbilitiesModel $dexAbilitiesModel,
	) {}

	/**
	 * Show the dex abilities page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilitiesModel->setData($generationIdentifier, $languageId);
	}
}
