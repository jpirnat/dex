<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexPokemonsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class DexPokemonsController
{
	private BaseController $baseController;
	private DexPokemonsModel $dexPokemonsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexPokemonsModel $dexPokemonsModel
	 */
	public function __construct(
		BaseController $baseController,
		DexPokemonsModel $dexPokemonsModel
	) {
		$this->baseController = $baseController;
		$this->dexPokemonsModel = $dexPokemonsModel;
	}

	/**
	 * Show the dex Pokémons page.
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

		$this->dexPokemonsModel->setData($generationIdentifier, $languageId);
	}
}
