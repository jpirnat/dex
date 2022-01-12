<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsMonthController
{
	public function __construct(
		private BaseController $baseController,
		private StatsMonthModel $statsMonthModel,
	) {}

	/**
	 * Get the formats list to recreate a stats month directory, such as
	 * http://www.smogon.com/stats/2014-11.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsMonthModel->setData(
			$month,
			$languageId
		);
	}
}
