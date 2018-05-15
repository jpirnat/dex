<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AbilityUsageMonth\AbilityUsageMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class AbilityUsageMonthController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var AbilityUsageMonthModel $abilityUsageMonthModel */
	private $abilityUsageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param AbilityUsageMonthModel $abilityUsageMonthModel
	 */
	public function __construct(
		BaseController $baseController,
		AbilityUsageMonthModel $abilityUsageMonthModel
	) {
		$this->baseController = $baseController;
		$this->abilityUsageMonthModel = $abilityUsageMonthModel;
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

		$this->abilityUsageMonthModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$abilityIdentifier,
			$languageId
		);
	}
}
