<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\MonthFormats\MonthFormatsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class MonthFormatsController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var MonthFormatsModel $monthFormatsModel */
	private $monthFormatsModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param MonthFormatsModel $monthFormatsModel
	 */
	public function __construct(
		BaseController $baseController,
		MonthFormatsModel $monthFormatsModel
	) {
		$this->baseController = $baseController;
		$this->monthFormatsModel = $monthFormatsModel;
	}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->monthFormatsModel->setData(
			$month,
			$languageId
		);
	}
}
