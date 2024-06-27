<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsMonthModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsMonthController
{
	public function __construct(
		private BaseController $baseController,
		private StatsMonthModel $statsMonthModel,
	) {}

	/**
	 * Set data for the stats month page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsMonthModel->setData(
			$month,
			$languageId,
		);
	}
}
