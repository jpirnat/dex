<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMoveFlagModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexMoveFlagController
{
	public function __construct(
		private BaseController $baseController,
		private DexMoveFlagModel $dexMoveFlagModel,
	) {}

	/**
	 * Set data for the dex move flag page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$flagIdentifier = $request->getAttribute('moveFlagIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexMoveFlagModel->setData($vgIdentifier, $flagIdentifier, $languageId);
	}
}
