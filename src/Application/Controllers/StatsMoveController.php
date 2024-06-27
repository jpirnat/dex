<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatsMoveModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatsMoveController
{
	public function __construct(
		private BaseController $baseController,
		private StatsMoveModel $statsMoveModel,
	) {}

	/**
	 * Set data for the stats move page.
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
			$languageId,
		);
	}
}
