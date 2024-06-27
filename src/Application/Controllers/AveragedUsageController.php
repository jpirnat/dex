<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AveragedUsageModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AveragedUsageController
{
	public function __construct(
		private BaseController $baseController,
		private AveragedUsageModel $averagedUsageModel,
	) {}

	/**
	 * Set data for the stats averaged usage page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->averagedUsageModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId,
		);
	}
}
