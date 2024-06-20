<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexTypeModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexTypeController
{
	public function __construct(
		private BaseController $baseController,
		private DexTypeModel $dexTypeModel,
	) {}

	/**
	 * Show the dex type page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$typeIdentifier = $request->getAttribute('typeIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexTypeModel->setData($vgIdentifier, $typeIdentifier, $languageId);
	}
}
