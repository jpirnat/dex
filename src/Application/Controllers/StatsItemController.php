<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsItemModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class StatsItemController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsItemModel $statsItemModel */
	private $statsItemModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsItemModel $statsItemModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsItemModel $statsItemModel
	) {
		$this->baseController = $baseController;
		$this->statsItemModel = $statsItemModel;
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

		$month = $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$itemIdentifier = $request->getAttribute('itemIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsItemModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$itemIdentifier,
			$languageId
		);
	}
}
