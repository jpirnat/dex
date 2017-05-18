<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AbilitiesModel;
use Psr\Http\Message\ServerRequestInterface;

class AbilitiesController
{
	/** @var AbilitiesModel $abilitiesModel */
	private $abilitiesModel;

	/**
	 * Constructor.
	 *
	 * @param AbilitiesModel $abilitiesModel
	 */
	public function __construct(
		AbilitiesModel $abilitiesModel
	) {
		$this->abilitiesModel = $abilitiesModel;
	}

	/**
	 * Get the ability usage history of the requested Pokémon in the requested
	 * format for the requested rating.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setRatingUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');

		$this->abilitiesModel->setRatingUsage(
			$formatIdentifier,
			$rating,
			$pokemonIdentifier
		);
	}

	/**
	 * Get the ability usage history of the requested Pokémon in the requested
	 * format for the requested ability across all ratings.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setAbilityUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$abilityIdentifier = $request->getAttribute('ability_identifier');

		$this->abilitiesModel->setAbilityUsage(
			$formatIdentifier,
			$pokemonIdentifier,
			$abilityIdentifier
		);
	}
}
