<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexNaturesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexNaturesController
{
	private BaseController $baseController;
	private DexNaturesModel $dexNaturesModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexNaturesModel $dexNaturesModel
	 */
	public function __construct(
		BaseController $baseController,
		DexNaturesModel $dexNaturesModel
	) {
		$this->baseController = $baseController;
		$this->dexNaturesModel = $dexNaturesModel;
	}

	/**
	 * Show the dex natures page.
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

		$this->dexNaturesModel->setData($generationIdentifier, $languageId);
	}
}
