<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatCalculator\StatCalculatorIndexModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatCalculatorIndexController
{
	public function __construct(
		private BaseController $baseController,
		private StatCalculatorIndexModel $statCalculatorIndexModel,
	) {}

	/**
	 * Set data for the stat calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->statCalculatorIndexModel->setData($vgIdentifier, $languageId);
	}
}
