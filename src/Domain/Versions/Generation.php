<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

class Generation
{
	/** @var GenerationId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param string $identifier
	 */
	public function __construct(
		GenerationId $generationId,
		string $identifier
	) {
		$this->id = $generationId;
		$this->identifier = $identifier;
	}

	/**
	 * Get the generation's id.
	 *
	 * @return GenerationId
	 */
	public function getId() : GenerationId
	{
		return $this->id;
	}

	/**
	 * Get the generation's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
