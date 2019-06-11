<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsAbilityController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsAbilityModel $statsAbilityModel */
	private $statsAbilityModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsAbilityModel $statsAbilityModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsAbilityModel $statsAbilityModel
	) {
		$this->baseController = $baseController;
		$this->statsAbilityModel = $statsAbilityModel;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
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
		$abilityIdentifier = $request->getAttribute('abilityIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsAbilityModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$abilityIdentifier,
			$languageId
		);
	}
}
