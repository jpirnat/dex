<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexMovesModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexMovesController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var DexMovesModel $dexMovesModel */
	private $dexMovesModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexMovesModel $dexMovesModel
	 */
	public function __construct(
		BaseController $baseController,
		DexMovesModel $dexMovesModel
	) {
		$this->baseController = $baseController;
		$this->dexMovesModel = $dexMovesModel;
	}

	/**
	 * Show the dex moves page.
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

		$this->dexMovesModel->setData($generationIdentifier, $languageId);
	}
}
