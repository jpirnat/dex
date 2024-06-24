<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexNaturesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexNaturesController
{
	public function __construct(
		private BaseController $baseController,
		private DexNaturesModel $dexNaturesModel,
	) {}

	/**
	 * Show the dex natures page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexNaturesModel->setData($vgIdentifier, $languageId);
	}
}
