<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsPokemonController
{
	public function __construct(
		private BaseController $baseController,
		private StatsPokemonModel $statsPokemonModel,
	) {}

	/**
	 * Set data for the stats Pokémon page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsPokemonModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$pokemonIdentifier,
			$languageId,
		);
	}
}
