<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\CookieNames;
use Jp\Dex\Application\Models\StatsUsageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsUsageController
{
	public function __construct(
		private BaseController $baseController,
		private StatsUsageModel $statsUsageModel,
	) {}

	/**
	 * Set data for the stats usage page.
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
			$languageId,
		);
	}
}
