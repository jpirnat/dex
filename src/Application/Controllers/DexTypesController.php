<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexTypesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexTypesController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var DexTypesModel $dexTypesModel */
	private $dexTypesModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexTypesModel $dexTypesModel
	 */
	public function __construct(
		BaseController $baseController,
		DexTypesModel $dexTypesModel
	) {
		$this->baseController = $baseController;
		$this->dexTypesModel = $dexTypesModel;
	}

	/**
	 * Show the dex types page.
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

		$this->dexTypesModel->setData($generationIdentifier, $languageId);
	}
}
