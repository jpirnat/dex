<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexPokemon\DexPokemonModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class DexPokemonController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var DexPokemonModel $dexPokemonModel */
	private $dexPokemonModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param DexPokemonModel $dexPokemonModel
	 */
	public function __construct(
		BaseController $baseController,
		DexPokemonModel $dexPokemonModel
	) {
		$this->baseController = $baseController;
		$this->dexPokemonModel = $dexPokemonModel;
	}

	/**
	 * Show the dex Pokémon page.
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
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexPokemonModel->setData($generationIdentifier, $pokemonIdentifier, $languageId);
	}
}
