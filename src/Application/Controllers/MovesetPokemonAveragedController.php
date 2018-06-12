<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\MovesetPokemonAveraged\MovesetPokemonAveragedModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class MovesetPokemonAveragedController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var MovesetPokemonAveragedModel $movesetPokemonAveragedModel */
	private $movesetPokemonAveragedModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param MovesetPokemonAveragedModel $movesetPokemonAveragedModel
	 */
	public function __construct(
		BaseController $baseController,
		MovesetPokemonAveragedModel $movesetPokemonAveragedModel
	) {
		$this->baseController = $baseController;
		$this->movesetPokemonAveragedModel = $movesetPokemonAveragedModel;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single
	 * Pokémon.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
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

		$this->movesetPokemonAveragedModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$pokemonIdentifier,
			$languageId
		);
	}
}
