<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AdvancedMoveSearch\AdvancedMoveSearchSubmitModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AdvancedMoveSearchSubmitController
{
	public function __construct(
		private AdvancedMoveSearchSubmitModel $advancedMoveSearchSubmitModel,
	) {}

	/**
	 * Set data for the advanced move search page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$typeIdentifiers = (array) ($data['typeIdentifiers'] ?? []);
		$categoryIdentifiers = (array) ($data['categoryIdentifiers'] ?? []);
		$yesFlagIdentifiers = (array) ($data['yesFlagIdentifiers'] ?? []);
		$noFlagIdentifiers = (array) ($data['noFlagsIdentifier'] ?? []);
		$pokemonIdentifier = (string) ($data['pokemonIdentifier'] ?? '');
		$includeTransferMoves = (string) ($data['includeTransferMoves'] ?? '');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->advancedMoveSearchSubmitModel->setData(
			$vgIdentifier,
			$typeIdentifiers,
			$categoryIdentifiers,
			$yesFlagIdentifiers,
			$noFlagIdentifiers,
			$pokemonIdentifier,
			$includeTransferMoves,
			$languageId,
		);
	}
}
