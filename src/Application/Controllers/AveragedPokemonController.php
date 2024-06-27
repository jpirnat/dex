<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AveragedPokemonModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AveragedPokemonController
{
	public function __construct(
		private BaseController $baseController,
		private AveragedPokemonModel $averagedPokemonModel,
	) {}

	/**
	 * Set data for the stats averaged Pokémon page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->averagedPokemonModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$pokemonIdentifier,
			$languageId,
		);
	}
}
