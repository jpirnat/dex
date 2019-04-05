<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsPokemon\StatsPokemonModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsPokemonController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsPokemonModel $statsPokemonModel */
	private $statsPokemonModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsPokemonModel $statsPokemonModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsPokemonModel $statsPokemonModel
	) {
		$this->baseController = $baseController;
		$this->statsPokemonModel = $statsPokemonModel;
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
			$languageId
		);
	}
}
