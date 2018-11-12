<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

interface CategoryRepositoryInterface
{
	/**
	 * Get all categories.
	 *
	 * @return Category[] Indexed by id.
	 */
	public function getAll() : array;
}
