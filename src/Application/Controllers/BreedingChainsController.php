<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\BreedingChains\BreedingChainsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class BreedingChainsController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var BreedingChainsModel $breedingChainsModel */
	private $breedingChainsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param BreedingChainsModel $breedingChainsModel
	 */
	public function __construct(
		BaseController $baseController,
		BreedingChainsModel $breedingChainsModel
	) {
		$this->baseController = $baseController;
		$this->breedingChainsModel = $breedingChainsModel;
	}

	/**
	 * Show the breeding chains page.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$generationIdentifier = $request->getAttribute('generationIdentifier');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$moveIdentifier = $request->getAttribute('moveIdentifier');
		$versionGroupIdentifier = $request->getAttribute('versionGroupIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->breedingChainsModel->setData(
			$generationIdentifier,
			$pokemonIdentifier,
			$moveIdentifier,
			$versionGroupIdentifier,
			$languageId
		);
	}
}
