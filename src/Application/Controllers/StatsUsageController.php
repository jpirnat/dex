<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\CookieNames;
use Jp\Dex\Application\Models\StatsUsageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class StatsUsageController
{
	public function __construct(
		private BaseController $baseController,
		private StatsUsageModel $statsUsageModel,
	) {}

	/**
	 * Get usage data to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$myFormat = $request->getCookieParams()[CookieNames::FORMAT] ?? '';
		$myRating = $request->getCookieParams()[CookieNames::RATING] ?? '';

		$this->statsUsageModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$myFormat,
			$myRating,
			$languageId
		);
	}
}
