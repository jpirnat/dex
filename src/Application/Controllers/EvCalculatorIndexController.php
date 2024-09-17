<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\EvCalculator\EvCalculatorIndexModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class EvCalculatorIndexController
{
	public function __construct(
		private BaseController $baseController,
		private EvCalculatorIndexModel $evCalculatorIndexModel,
	) {}

	/**
	 * Set data for the EV calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->evCalculatorIndexModel->setData($vgIdentifier, $languageId);
	}
}
