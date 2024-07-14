<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TextLinks;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface TextLinkRepositoryInterface
{
	/**
	 * Get a text link for this item.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForItem(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		ItemId $itemId,
	) : TextLinkItem;

	/**
	 * Get a text link for this move.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForMove(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		MoveId $moveId,
	) : TextLinkMove;

	/**
	 * Get a text link for this Pokémon.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForPokemon(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		PokemonId $pokemonId,
	) : TextLinkPokemon;

	/**
	 * Get a text link for this type.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForType(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		TypeId $typeId,
	) : TextLinkType;
}
