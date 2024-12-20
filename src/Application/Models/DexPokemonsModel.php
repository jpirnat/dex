<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexPokemonsModel
{
	private(set) array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
	) {}


	/**
	 * Set data for the dex Pokémons page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);

		$this->pokemon = $this->dexPokemonRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
	}
}
