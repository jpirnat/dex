<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexTmsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexTmsController
{
	public function __construct(
		private BaseController $baseController,
		private DexTmsModel $dexTmsModel,
	) {}

	/**
	 * Set data for the dex TMs page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexTmsModel->setData($vgIdentifier, $languageId);
	}
}
