<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Languages\LanguageId;

final class MoveMethodName
{
	public function __construct(
		private LanguageId $languageId,
		private MoveMethodId $moveMethodId,
		private string $name,
		private string $description,
	) {}

	/**
	 * Get the move method name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move method name's move method id.
	 *
	 * @return MoveMethodId
	 */
	public function getMoveMethodId() : MoveMethodId
	{
		return $this->moveMethodId;
	}

	/**
	 * Get the move method name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the move method name's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
