<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;

class MoveName
{
	/** @var LanguageId $languageId */
	protected $languageId;

	/** @var MoveId $moveId */
	protected $moveId;

	/** @var string $name */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		MoveId $moveId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->moveId = $moveId;
		$this->name = $name;
	}

	/**
	 * Get the move name's language id.
	 *
	 * @return LanguageId
	 */
	public function languageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move name's move id.
	 *
	 * @return MoveId
	 */
	public function moveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the move name's name value.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
	}
}
