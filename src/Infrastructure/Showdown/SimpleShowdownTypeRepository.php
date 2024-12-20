<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\Showdown\ShowdownTypeRepositoryInterface;
use Jp\Dex\Domain\Import\Showdown\TypeNotImportedException;
use Jp\Dex\Domain\Types\TypeId;

final class SimpleShowdownTypeRepository implements ShowdownTypeRepositoryInterface
{
	/** @var TypeId[] $typesToImport */
	private array $typesToImport;

	/** @var array<string, int> $typesToIgnore */
	private array $typesToIgnore;

	/** @var string[] $unknownTypes */
	private array $unknownTypes = [];


	public function __construct()
	{
		$this->typesToImport = [
			'Normal' => new TypeId(0),
			'Fighting' => new TypeId(1),
			'Flying' => new TypeId(2),
			'Poison' => new TypeId(3),
			'Ground' => new TypeId(4),
			'Rock' => new TypeId(5),
			'Bug' => new TypeId(6),
			'Ghost' => new TypeId(7),
			'Steel' => new TypeId(8),
			'Fire' => new TypeId(9),
			'Water' => new TypeId(10),
			'Grass' => new TypeId(11),
			'Electric' => new TypeId(12),
			'Psychic' => new TypeId(13),
			'Ice' => new TypeId(14),
			'Dragon' => new TypeId(15),
			'Dark' => new TypeId(16),
			'Fairy' => new TypeId(17),
			'Stellar' => new TypeId(18),
		];

		$this->typesToIgnore = [
			'Nothing' => 1,
			'Other' => 1,
		];
	}

	/**
	 * Is the Pokémon Showdown type name known and imported?
	 */
	public function isImported(string $showdownTypeName) : bool
	{
		return isset($this->typesToImport[$showdownTypeName]);
	}

	/**
	 * Is the Pokémon Showdown type name known and ignored?
	 */
	public function isIgnored(string $showdownTypeName) : bool
	{
		return isset($this->typesToIgnore[$showdownTypeName]);
	}

	/**
	 * Is the Pokémon Showdown type name known?
	 */
	public function isKnown(string $showdownTypeName) : bool
	{
		return $this->isImported($showdownTypeName)
			|| $this->isIgnored($showdownTypeName)
		;
	}

	/**
	 * Add a Pokémon Showdown type name to the list of unknown types.
	 */
	public function addUnknown(string $showdownTypeName) : void
	{
		$this->unknownTypes[$showdownTypeName] = $showdownTypeName;
	}

	/**
	 * Get the type id of a Pokémon Showdown type name.
	 *
	 * @throws TypeNotImportedException if $showdownTypeName is not an
	 *     imported type name.
	 */
	public function getTypeId(string $showdownTypeName) : TypeId
	{
		// If the type is imported, return the type id.
		if ($this->isImported($showdownTypeName)) {
			return $this->typesToImport[$showdownTypeName];
		}

		// If the type is not known, add it to the list of unknown types.
		if (!$this->isKnown($showdownTypeName)) {
			$this->addUnknown($showdownTypeName);
		}

		throw new TypeNotImportedException(
			"Type should not be imported: $showdownTypeName."
		);
	}

	/**
	 * Get the names of the unknown types the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownTypes;
	}
}
