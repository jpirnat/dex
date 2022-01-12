<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsAbilityModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsAbilityController
{
	public function __construct(
		private BaseController $baseController,
		private StatsAbilityModel $statsAbilityModel,
	) {}

	/**
	 * Get usage data to create a list of Pokémon who use a specific ability.
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
