<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Languages\LanguageId;

interface MoveMethodNameRepositoryInterface
{
	/**
	 * Get move method names by language.
	 *
	 * @param LanguageId $languageId
	 *
	 * @return MoveMethodName[] Indexed by move method id.
	 */
	public function getByLanguage(LanguageId $languageId) : array;
}
