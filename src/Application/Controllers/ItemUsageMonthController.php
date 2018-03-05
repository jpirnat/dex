<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\ItemUsageMonth\ItemUsageMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class ItemUsageMonthController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var ItemUsageMonthModel $itemUsageMonthModel */
	private $itemUsageMonthModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param ItemUsageMonthModel $itemUsageMonthModel
	 */
	public function __construct(
		BaseController $baseController,
		ItemUsageMonthModel $itemUsageMonthModel
	) {
		$this->baseController = $baseController;
		$this->itemUsageMonthModel = $itemUsageMonthModel;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific item.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$year = (int) $request->getAttribute('year');
		$month = (int) $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$itemIdentifier = $request->getAttribute('itemIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->itemUsageMonthModel->setData(
			$year,
			$month,
			$formatIdentifier,
			$rating,
			$itemIdentifier,
			$languageId
		);
	}
}
