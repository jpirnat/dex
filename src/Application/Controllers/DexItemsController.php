<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexItemsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexItemsController
{
	public function __construct(
		private BaseController $baseController,
		private DexItemsModel $dexItemsModel,
	) {}

	/**
	 * Set data for the dex items page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexItemsModel->setData($vgIdentifier, $languageId);
	}
}
