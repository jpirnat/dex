<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\Languages\Language;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final class AbilityDescriptionImporter
{
    /** @var string[][] $names Indexed by language id, then ability id. */
    private array $names = [];

    /** @var string[][] $descriptions Indexed by language id, then ability id. */
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

        return "https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/rom-txt/$subdirectory/tokusei.json";
    }

    private function getDescriptionsUrl(Language $language): string
    {
        $subdirectory = $language->champoutSubdirectory;

        return "https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/rom-txt/$subdirectory/tokuseiinfo_syn.json";
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

        foreach ($json['mSDataSet'] ?? [] as $ability) {
            $labelName = (string) ($ability['LabelName'] ?? '');
            $abilityId = $this->getAbilityIdFromNameLabelName($labelName);

            $name = (string) ($ability['OriginalText'] ?? '');
            $name = str_replace("\n", '\\n', $name);

            $this->names[$language->id->value][$abilityId] = $name;
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

        foreach ($json['mSDataSet'] ?? [] as $ability) {
            $labelName = (string) ($ability['LabelName'] ?? '');
            $abilityId = $this->getAbilityIdFromDescriptionLabelName($labelName);

            $description = (string) ($ability['OriginalText'] ?? '');
            $description = str_replace("\n", '\\n', $description);

            $this->descriptions[$language->id->value][$abilityId] = $description;
        }
    }

    private function getAbilityIdFromNameLabelName(string $labelName): int
    {
        $pattern = '/TOKUSEI_(\d+)/';

        try {
            $matchResult = Regex::match($pattern, $labelName);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $abilityId = $matchResult->group(1);
        } catch (RegexFailed $e) {
            echo $e->getMessage();
            exit;
        }

        return (int) $abilityId;
    }

    private function getAbilityIdFromDescriptionLabelName(string $labelName): int
    {
        $pattern = '/TOKUSEIINFO_SYN_(\d+)/';

        try {
            $matchResult = Regex::match($pattern, $labelName);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $abilityId = $matchResult->group(1);
        } catch (RegexFailed $e) {
            echo $e->getMessage();
            exit;
        }

        return (int) $abilityId;
    }

    private function exportCsv(): void
    {
        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'version_group_id',
            'language_id',
            'ability_id',
            'name',
            'description',
        ]);
        foreach ($this->names as $languageId => $names) {
            foreach ($names as $abilityId => $name) {
                $description = $this->descriptions[$languageId][$abilityId] ?? '';

                $csv->insertOne([
                    VersionGroupId::CHAMPIONS,
                    $languageId,
                    $abilityId,
                    $name,
                    $description,
                ]);
            }
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/ability_descriptions_champions.csv",
            $csv->toString(),
        );
    }
}
