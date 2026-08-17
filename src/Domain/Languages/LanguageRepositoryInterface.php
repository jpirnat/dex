<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Languages;

use Jp\Dex\Domain\Versions\VersionGroupId;

interface LanguageRepositoryInterface
{
    /**
     * Get a language by its id.
     *
     * @throws LanguageNotFoundException if no language exists with this id.
     */
    public function getById(LanguageId $languageId): Language;

    /**
     * Get languages in this version group.
     *
     * @return Language[] Indexed by id.
     */
    public function getInVersionGroup(VersionGroupId $versionGroupId): array;
}
