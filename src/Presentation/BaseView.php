<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BaseModel;

final class BaseView
{
	public function __construct(
		private BaseModel $baseModel,
	) {}

	/**
	 * Get the variables needed for the base template.
	 */
	public function getBaseVariables() : array
	{
		return [
			'currentLanguageId' => $this->baseModel->getCurrentLanguageId()->value(),
			'languages' => $this->baseModel->getLanguages(),
		];
	}
}
