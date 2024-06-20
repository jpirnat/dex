<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonsModel
{
	private array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private array $pokemon = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private StatNameModel $statNameModel,
	) {}


	/**
	 * Set data for the dex Pokémons page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByVersionGroup($versionGroupId, $languageId);

		$this->pokemon = $this->dexPokemonRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
	}


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the stats and their names.
	 */
	public function getStats() : array
	{
		return $this->stats;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return DexPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}
}
