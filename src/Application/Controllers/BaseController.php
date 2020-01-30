<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\BaseModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final class BaseController
{
	private BaseModel $baseModel;

	/**
	 * Constructor.
	 *
	 * @param BaseModel $baseModel
	 */
	public function __construct(BaseModel $baseModel)
	{
		$this->baseModel = $baseModel;
	}

	/**
	 * Set the variables needed for the base template.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setBaseVariables(ServerRequestInterface $request) : void
	{
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->baseModel->setCurrentLanguageId($languageId);
	}
}
