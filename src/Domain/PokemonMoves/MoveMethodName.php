<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Languages\LanguageId;

final class MoveMethodName
{
	/** @var LanguageId $languageId */
	private $languageId;

	/** @var MoveMethodId $moveMethodId */
	private $moveMethodId;

	/** @var string $name */
	private $name;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param MoveMethodId $moveMethodId
	 * @param string $name
	 * @param string $description
	 */
	public function __construct(
		LanguageId $languageId,
		MoveMethodId $moveMethodId,
		string $name,
		string $description
	) {
		$this->languageId = $languageId;
		$this->moveMethodId = $moveMethodId;
		$this->name = $name;
		$this->description = $description;
	}

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
