<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexEggGroupsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexEggGroupsController
{
	public function __construct(
		private BaseController $baseController,
		private DexEggGroupsModel $dexEggGroupsModel,
	) {}

	/**
	 * Set data for the dex egg group page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexEggGroupsModel->setData($vgIdentifier, $languageId);
	}
}
