<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexItemModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexItemController
{
	public function __construct(
		private BaseController $baseController,
		private DexItemModel $dexItemModel,
	) {}

	/**
	 * Set data for the dex item page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$itemIdentifier = $request->getAttribute('itemIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexItemModel->setData($vgIdentifier, $itemIdentifier, $languageId);
	}
}
