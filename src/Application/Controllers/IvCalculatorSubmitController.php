<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorSubmitModel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class IvCalculatorSubmitController
{
	public function __construct(
		private IvCalculatorSubmitModel $ivCalculatorSubmitModel,
	) {}

	/**
	 * Set data for the IV calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$pokemonIdentifier = (string) ($data['pokemonIdentifier'] ?? '');
		$natureIdentifier = (string) ($data['natureIdentifier'] ?? '') ?: 'hardy';
		$characteristicIdentifier = (string) ($data['characteristicIdentifier'] ?? '');
		$hpTypeIdentifier = (string) ($data['hpTypeIdentifier'] ?? '');
		$atLevel = (array) ($data['atLevel'] ?? []);

		$this->ivCalculatorSubmitModel->setData(
			$vgIdentifier,
			$pokemonIdentifier,
			$natureIdentifier,
			$characteristicIdentifier,
			$hpTypeIdentifier,
			$atLevel,
		);
	}
}
