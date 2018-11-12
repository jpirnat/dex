<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

class Category
{
	/** @var CategoryId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var string $icon */
	private $icon;

	/**
	 * Constructor.
	 *
	 * @param CategoryId $categoryId
	 * @param string $identifier
	 * @param string $icon
	 */
	public function __construct(
		CategoryId $categoryId,
		string $identifier,
		string $icon
	) {
		$this->id = $categoryId;
		$this->identifier = $identifier;
		$this->icon = $icon;
	}

	/**
	 * Get the category's id.
	 *
	 * @return CategoryId
	 */
	public function getId() : CategoryId
	{
		return $this->id;
	}

	/**
	 * Get the category's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the category's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}
}
