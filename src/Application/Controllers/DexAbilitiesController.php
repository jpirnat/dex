<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexAbilitiesController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var DexAbilitiesModel $dexAbilitiesModel */
	private $dexAbilitiesModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexAbilitiesModel $dexAbilitiesModel
	 */
	public function __construct(
		BaseController $baseController,
		DexAbilitiesModel $dexAbilitiesModel
	) {
		$this->baseController = $baseController;
		$this->dexAbilitiesModel = $dexAbilitiesModel;
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexAbilitiesModel->setData($generationIdentifier, $languageId);
	}
}
