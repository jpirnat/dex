<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\StatCalculator\StatCalculatorSubmitModel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class StatCalculatorSubmitController
{
	public function __construct(
		private StatCalculatorSubmitModel $statCalculatorSubmitModel,
	) {}

	/**
	 * Set data for the stat calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$pokemonIdentifier = (string) ($data['pokemonIdentifier'] ?? '');
		$natureIdentifier = (string) ($data['natureIdentifier'] ?? '') ?: 'hardy';
		$level = (string) ($data['level'] ?? '');
		$friendship = (string) ($data['friendship'] ?? '');
		$ivs = (array) ($data['ivs'] ?? []);
		$evs = (array) ($data['evs'] ?? []);
		$avs = (array) ($data['avs'] ?? []);
		$effortLevels = (array) ($data['effortLevels'] ?? []);

		$this->statCalculatorSubmitModel->setData(
			$vgIdentifier,
			$pokemonIdentifier,
			$natureIdentifier,
			$level,
			$friendship,
			$ivs,
			$evs,
			$avs,
			$effortLevels,
		);
	}
}
