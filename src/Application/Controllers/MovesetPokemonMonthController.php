<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\MovesetPokemonMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class MovesetPokemonMonthController
{
	/** @var MovesetPokemonMonthModel $movesetPokemonMonthModel */
	private $movesetPokemonMonthModel;

	/**
	 * Constructor.
	 *
	 * @param MovesetPokemonMonthModel $movesetPokemonMonthModel
	 */
	public function __construct(
		MovesetPokemonMonthModel $movesetPokemonMonthModel
	) {
		$this->movesetPokemonMonthModel = $movesetPokemonMonthModel;
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
		$year = (int) $request->getAttribute('year');
		$month = (int) $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('language'));

		$this->movesetPokemonMonthModel->setData(
			$year,
			$month,
			$formatIdentifier,
			$rating,
			$pokemonIdentifier,
			$languageId
		);
	}
}
