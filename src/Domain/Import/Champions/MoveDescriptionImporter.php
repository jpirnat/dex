<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use Exception;
use Jp\Dex\Domain\Languages\Language;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\Csv\Bom;
use League\Csv\Writer;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final class MoveDescriptionImporter
{
    /** @var string[][] $names Indexed by language id, then move id. */
    private array $names = [];

    /** @var string[][] $descriptions Indexed by language id, then move id. */
    private array $descriptions = [];

    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly Client $client,
        private readonly string $projectRoot,
    ) {}

    public function import(): void
    {
        $this->names = [];
        $this->descriptions = [];

        $champions = new VersionGroupId(VersionGroupId::CHAMPIONS);
        $languages = $this->languageRepository->getInVersionGroup($champions);

        foreach ($languages as $language) {
            $url = $this->getNamesUrl($language);
            $this->processNamesUrl($language, $url);

            $url = $this->getDescriptionsUrl($language);
            $this->processDescriptionsUrl($language, $url);
        }

        $this->exportCsv();
    }

    private function getNamesUrl(Language $language): string
    {
        $subdirectory = $language->champoutSubdirectory;

        return "https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/rom-txt/$subdirectory/wazaname.json";
    }

    private function getDescriptionsUrl(Language $language): string
    {
        $subdirectory = $language->champoutSubdirectory;

        return "https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/rom-txt/$subdirectory/wazainfo_syn.json";
    }

    private function processNamesUrl(Language $language, string $url): void
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (GuzzleException $e) {
            echo $e->getMessage();
            exit;
        }

        $html = $response->getBody()->getContents();

        $json = json_decode($html, true);

        foreach ($json['mSDataSet'] ?? [] as $move) {
            $labelName = (string) ($move['LabelName'] ?? '');
            $moveId = $this->getMoveIdFromNameLabelName($labelName);

            $name = (string) ($move['OriginalText'] ?? '');
            $name = str_replace("\n", '\\n', $name);

            $this->names[$language->id->value][$moveId] = $name;
        }
    }

    private function processDescriptionsUrl(Language $language, string $url): void
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (GuzzleException $e) {
            echo $e->getMessage();
            exit;
        }

        $html = $response->getBody()->getContents();

        $json = json_decode($html, true);

        foreach ($json['mSDataSet'] ?? [] as $move) {
            $labelName = (string) ($move['LabelName'] ?? '');
            $moveId = $this->getMoveIdFromDescriptionLabelName($labelName);

            $description = (string) ($move['OriginalText'] ?? '');
            $description = str_replace("\n", '\\n', $description);

            $this->descriptions[$language->id->value][$moveId] = $description;
        }
    }

    private function getMoveIdFromNameLabelName(string $labelName): int
    {
        $pattern = '/WAZANAME_(\d+)/';

        try {
            $matchResult = Regex::match($pattern, $labelName);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $moveId = $matchResult->group(1);
        } catch (RegexFailed $e) {
            echo $e->getMessage();
            exit;
        }

        return (int) $moveId;
    }

    private function getMoveIdFromDescriptionLabelName(string $labelName): int
    {
        $pattern = '/WAZAINFO_SYN_(\d+)/';

        try {
            $matchResult = Regex::match($pattern, $labelName);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $moveId = $matchResult->group(1);
        } catch (RegexFailed $e) {
            echo $e->getMessage();
            exit;
        }

        return (int) $moveId;
    }

    private function exportCsv(): void
    {
        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'version_group_id',
            'language_id',
            'move_id',
            'name',
            'description',
        ]);
        foreach ($this->names as $languageId => $names) {
            foreach ($names as $moveId => $name) {
                $description = $this->descriptions[$languageId][$moveId] ?? '';

                $csv->insertOne([
                    VersionGroupId::CHAMPIONS,
                    $languageId,
                    $moveId,
                    $name,
                    $description,
                ]);
            }
        }

        file_put_contents(
            "$this->projectRoot/ignore/tables/move_descriptions_champions.csv",
            $csv->toString(),
        );
    }
}
