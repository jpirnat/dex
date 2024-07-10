<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Forms;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface FormRepositoryInterface
{
	/**
	 * Get a form by its id.
	 *
	 * @throws FormNotFoundException if no form exists with this id.
	 */
	public function getById(FormId $formId) : Form;

	/**
	 * Get form ids of this Pokémon, available in this version group.
	 *
	 * @return FormId[] Indexed by id.
	 */
	public function getByVgAndPokemon(VersionGroupId $versionGroupId, PokemonId $pokemonId) : array;
}
