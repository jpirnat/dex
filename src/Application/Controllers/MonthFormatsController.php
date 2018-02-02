<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\MonthFormats\MonthFormatsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class MonthFormatsController
{
	/** @var MonthFormatsModel $monthFormatsModel */
	private $monthFormatsModel;

	/**
	 * Constructor.
	 *
	 * @param MonthFormatsModel $monthFormatsModel
	 */
	public function __construct(
		MonthFormatsModel $monthFormatsModel
	) {
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
		$year = (int) $request->getAttribute('year');
		$month = (int) $request->getAttribute('month');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->monthFormatsModel->setData(
			$year,
			$month,
			$languageId
		);
	}
}
