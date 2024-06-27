<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilityModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexAbilityController
{
	public function __construct(
		private BaseController $baseController,
		private DexAbilityModel $dexAbilityModel,
	) {}

	/**
	 * Set data for the dex ability page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$abilityIdentifier = $request->getAttribute('abilityIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilityModel->setData($vgIdentifier, $abilityIdentifier, $languageId);
	}
}
