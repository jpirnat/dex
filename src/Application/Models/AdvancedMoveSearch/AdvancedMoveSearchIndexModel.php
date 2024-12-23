<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedMoveSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\Flags\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class AdvancedMoveSearchIndexModel
{
	/** @var DexType[] $types */
	private(set) array $types = [];
	/** @var DexCategory[] $categories */
	private(set) array $categories = [];
	private(set) array $flags = [];
	private(set) array $pokemons = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly DexCategoryRepositoryInterface $dexCategoryRepository,
		private readonly MoveFlagRepositoryInterface $flagRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
	) {}


	/**
	 * Set data for the advanced move search page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->pokemons = [];
		$this->types = [];
		$this->categories = [];
		$this->flags = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->types = $this->dexTypeRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$this->categories = $this->dexCategoryRepository->getByLanguage(
			$languageId,
		);

		$flags = $this->flagRepository->getByVersionGroupPlural(
			$versionGroupId,
			$languageId,
		);
		foreach ($flags as $flag) {
			$this->flags[] = [
				'identifier' => $flag->identifier,
				'name' => $flag->name,
				'description' => strip_tags($flag->description),
			];
		}

		$pokemons = $this->dexPokemonRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		foreach ($pokemons as $pokemon) {
			$this->pokemons[] = [
				'identifier' => $pokemon->identifier,
				'name' => $pokemon->name,
			];
		}
		usort($this->pokemons, function (array $a, array $b) : int {
			return $a['name'] <=> $b['name'];
		});
	}
}
