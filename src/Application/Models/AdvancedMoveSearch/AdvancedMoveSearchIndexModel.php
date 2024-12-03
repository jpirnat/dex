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
	private array $types = [];
	/** @var DexCategory[] $categories */
	private array $categories = [];
	private array $flags = [];
	private array $pokemons = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
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
				'identifier' => $flag->getIdentifier(),
				'name' => $flag->getName(),
				'description' => strip_tags($flag->getDescription()),
			];
		}

		$pokemons = $this->dexPokemonRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		foreach ($pokemons as $pokemon) {
			$this->pokemons[] = [
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
			];
		}
		usort($this->pokemons, function (array $a, array $b) : int {
			return $a['name'] <=> $b['name'];
		});
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * @return DexCategory[]
	 */
	public function getCategories() : array
	{
		return $this->categories;
	}

	public function getFlags() : array
	{
		return $this->flags;
	}

	public function getPokemons() : array
	{
		return $this->pokemons;
	}
}
