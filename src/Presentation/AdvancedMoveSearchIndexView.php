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
		$versionGroupModel = $this->advancedMoveSearchIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$types = $this->advancedMoveSearchIndexModel->getTypes();
		$categories = $this->advancedMoveSearchIndexModel->getCategories();
		$flags = $this->advancedMoveSearchIndexModel->getFlags();
		$pokemons = $this->advancedMoveSearchIndexModel->getPokemons();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
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
					'identifier' => $versionGroup->getIdentifier(),
					'hasTransferMoves' => $versionGroup->hasTransferMoves(),
					'hasMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
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
