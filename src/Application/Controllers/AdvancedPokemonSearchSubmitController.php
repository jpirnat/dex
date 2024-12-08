<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AdvancedPokemonSearch\AdvancedPokemonSearchSubmitModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AdvancedPokemonSearchSubmitController
{
	public function __construct(
		private AdvancedPokemonSearchSubmitModel $advancedPokemonSearchSubmitModel,
	) {}

	/**
	 * Set data for the advanced Pokémon search page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$abilityIdentifier = (string) ($data['abilityIdentifier'] ?? '');
		$moveIdentifiers = (array) ($data['moveIdentifiers'] ?? []);
		$includeTransferMoves = (string) ($data['includeTransferMoves'] ?? '');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->advancedPokemonSearchSubmitModel->setData(
			$vgIdentifier,
			$abilityIdentifier,
			$moveIdentifiers,
			$includeTransferMoves,
			$languageId,
		);
	}
}
