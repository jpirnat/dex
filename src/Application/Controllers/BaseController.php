<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\BaseModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class BaseController
{
	public function __construct(
		private BaseModel $baseModel,
	) {}

	/**
	 * Set the variables needed for the base template.
	 */
	public function setBaseVariables(ServerRequestInterface $request) : void
	{
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->baseModel->setData($languageId);
	}
}
