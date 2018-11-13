<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexTypeModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class DexTypeController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var DexTypeModel $dexTypeModel */
	private $dexTypeModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexTypeModel $dexTypeModel
	 */
	public function __construct(
		BaseController $baseController,
		DexTypeModel $dexTypeModel
	) {
		$this->baseController = $baseController;
		$this->dexTypeModel = $dexTypeModel;
	}

	/**
	 * Show the dex type page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$typeIdentifier = $request->getAttribute('typeIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexTypeModel->setData($generationIdentifier, $typeIdentifier, $languageId);
	}
}
