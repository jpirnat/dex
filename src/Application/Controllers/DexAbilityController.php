<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilityModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexAbilityController
{
	private BaseController $baseController;
	private DexAbilityModel $dexAbilityModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexAbilityModel $dexAbilityModel
	 */
	public function __construct(
		BaseController $baseController,
		DexAbilityModel $dexAbilityModel
	) {
		$this->baseController = $baseController;
		$this->dexAbilityModel = $dexAbilityModel;
	}

	/**
	 * Show the dex ability page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$abilityIdentifier = $request->getAttribute('abilityIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilityModel->setData($generationIdentifier, $abilityIdentifier, $languageId);
	}
}
