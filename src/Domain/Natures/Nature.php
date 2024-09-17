<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Stats\StatId;

final readonly class Nature
{
	public function __construct(
		private NatureId $id,
		private string $identifier,
		private ?StatId $increasedStatId,
		private ?StatId $decreasedStatId,
		private FormId $toxelEvoId,
		private int $vcExpRemainder,
	) {}

	public function getId() : NatureId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getIncreasedStatId() : ?StatId
	{
		return $this->increasedStatId;
	}

	public function getDecreasedStatId() : ?StatId
	{
		return $this->decreasedStatId;
	}

	public function getToxelEvoId() : FormId
	{
		return $this->toxelEvoId;
	}

	public function getVcExpRemainder() : int
	{
		return $this->vcExpRemainder;
	}
}
