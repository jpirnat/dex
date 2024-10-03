<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexEggGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexEggGroupController
{
	public function __construct(
		private BaseController $baseController,
		private DexEggGroupModel $dexEggGroupModel,
	) {}

	/**
	 * Set data for the dex egg group page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$eggGroupIdentifier = $request->getAttribute('eggGroupIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexEggGroupModel->setData($vgIdentifier, $eggGroupIdentifier, $languageId);
	}
}
