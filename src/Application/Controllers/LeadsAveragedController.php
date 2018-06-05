<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\LeadsAveraged\LeadsAveragedModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class LeadsAveragedController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var LeadsAveragedModel $leadsAveragedModel */
	private $leadsAveragedModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param LeadsAveragedModel $leadsAveragedModel
	 */
	public function __construct(
		BaseController $baseController,
		LeadsAveragedModel $leadsAveragedModel
	) {
		$this->baseController = $baseController;
		$this->leadsAveragedModel = $leadsAveragedModel;
	}

	/**
	 * Get leads data averaged over multiple months.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->leadsAveragedModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
