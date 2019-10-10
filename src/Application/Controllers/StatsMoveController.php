<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsMoveModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsMoveController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var StatsMoveModel $statsMoveModel */
	private $statsMoveModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param StatsMoveModel $statsMoveModel
	 */
	public function __construct(
		BaseController $baseController,
		StatsMoveModel $statsMoveModel
	) {
		$this->baseController = $baseController;
		$this->statsMoveModel = $statsMoveModel;
	}

	/**
	 * Get usage data to create a list of Pokémon who use a specific move.
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
		$moveIdentifier = $request->getAttribute('moveIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsMoveModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$moveIdentifier,
			$languageId
		);
	}
}
