<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilityFlagModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexAbilityFlagController
{
	public function __construct(
		private BaseController $baseController,
		private DexAbilityFlagModel $dexAbilityFlagModel,
	) {}

	/**
	 * Set data for the dex ability flag page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$flagIdentifier = $request->getAttribute('abilityFlagIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilityFlagModel->setData($vgIdentifier, $flagIdentifier, $languageId);
	}
}
