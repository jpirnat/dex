<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\LeadsModel;
use Psr\Http\Message\ServerRequestInterface;

class LeadsController
{
	/** @var LeadsModel $leadsModel */
	private $leadsModel;

	/**
	 * Constructor.
	 *
	 * @param LeadsModel $leadsModel
	 */
	public function __construct(
		LeadsModel $leadsModel
	) {
		$this->leadsModel = $leadsModel;
	}

	/**
	 * Get the lead usage history of the requested Pokémon in the requested
	 * format across all ratings.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');

		$this->leadsModel->setUsage($formatIdentifier, $pokemonIdentifier);
	}
}
