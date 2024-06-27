<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsLeadsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsLeadsController
{
	public function __construct(
		private BaseController $baseController,
		private StatsLeadsModel $statsLeadsModel,
	) {}

	/**
	 * Set data for the stats leads page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$month = $request->getAttribute('month');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statsLeadsModel->setData(
			$month,
			$formatIdentifier,
			$rating,
			$languageId,
		);
	}
}
