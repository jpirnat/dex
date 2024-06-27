<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexPokemon\DexPokemonModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexPokemonController
{
	public function __construct(
		private BaseController $baseController,
		private DexPokemonModel $dexPokemonModel,
	) {}

	/**
	 * Set data for the dex Pokémon page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexPokemonModel->setData($vgIdentifier, $pokemonIdentifier, $languageId);
	}
}
