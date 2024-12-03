<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AdvancedMoveSearch\AdvancedMoveSearchIndexModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AdvancedMoveSearchIndexController
{
	public function __construct(
		private BaseController $baseController,
		private AdvancedMoveSearchIndexModel $advancedMoveSearchIndexModel,
	) {}

	/**
	 * Set data for the advanced move search page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->advancedMoveSearchIndexModel->setData($vgIdentifier, $languageId);
	}
}
