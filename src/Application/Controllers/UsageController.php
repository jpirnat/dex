<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\UsageModel;
use Psr\Http\Message\ServerRequestInterface;

class UsageController
{
	/** @var UsageModel $usageModel */
	private $usageModel;

	/**
	 * Constructor.
	 *
	 * @param UsageModel $usageModel
	 */
	public function __construct(
		UsageModel $usageModel
	) {
		$this->usageModel = $usageModel;
	}

	/**
	 * Get the usage history of the requested Pokémon in the requested format
	 * across all ratings.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('format_identifier');
		$pokemonIdentifier = $request->getAttribute('pokemon_identifier');

		$this->usageModel->setUsage($formatIdentifier, $pokemonIdentifier);
	}
}
