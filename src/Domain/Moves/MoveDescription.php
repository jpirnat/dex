<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class MoveDescription
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private LanguageId $languageId,
		private MoveId $moveId,
		private string $description,
	) {}

	/**
	 * Get the move description's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the move description's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move description's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the move description's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
