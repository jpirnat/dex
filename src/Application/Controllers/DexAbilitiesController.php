<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexAbilitiesController
{
	public function __construct(
		private BaseController $baseController,
		private DexAbilitiesModel $dexAbilitiesModel,
	) {}

	/**
	 * Set data for the dex abilities page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilitiesModel->setData($vgIdentifier, $languageId);
	}
}
