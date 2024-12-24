<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AdvancedMoveSearch\AdvancedMoveSearchIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class AdvancedMoveSearchIndexView
{
	public function __construct(
		private AdvancedMoveSearchIndexModel $advancedMoveSearchIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the advanced move search page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->advancedMoveSearchIndexModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$types = $this->advancedMoveSearchIndexModel->types;
		$categories = $this->advancedMoveSearchIndexModel->categories;
		$flags = $this->advancedMoveSearchIndexModel->flags;
		$pokemons = $this->advancedMoveSearchIndexModel->pokemons;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/moves",
			'text' => 'Moves',
		], [
			'text' => 'Advanced Search',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasTransferMoves' => $versionGroup->hasTransferMoves,
					'hasMoveDescriptions' => $versionGroup->id->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'types' => $this->dexFormatter->formatDexTypes($types),
				'categories' => $this->dexFormatter->formatDexCategories($categories),
				'flags' => $flags,
				'pokemons' => $pokemons,
			]
		]);
	}
}
