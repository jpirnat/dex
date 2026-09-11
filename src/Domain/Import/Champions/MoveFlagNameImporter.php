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
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

final class MoveFlagNameImporter
{
    /** @var string[][] $names Indexed by flag id, then language id. */
    private array $names = [];

    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly Client $client,
        private readonly string $projectRoot,
    ) {}

    public function import(): void
    {
        $this->names = [];

        $champions = new VersionGroupId(VersionGroupId::CHAMPIONS);
        $languages = $this->languageRepository->getInVersionGroup($champions);

        foreach ($languages as $language) {
            $url = $this->getUrl($language);
            $this->processUrl($language, $url);
        }

        $this->exportCsv();
    }

    private function getUrl(Language $language): string
    {
        $subdirectory = $language->champoutSubdirectory;

        return "https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/rom-txt/$subdirectory/wazaclassification.json";
    }

    private function processUrl(Language $language, string $url): void
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (GuzzleException $e) {
            echo $e->getMessage();
            exit;
        }

        $html = $response->getBody()->getContents();

        $json = json_decode($html, true);

        foreach ($json['mSDataSet'] ?? [] as $flag) {
            $labelName = (string) ($flag['LabelName'] ?? '');
            $flagId = $this->getFlagIdFromLabelName($labelName);

            $name = (string) ($flag['OriginalText'] ?? '');
            $name = str_replace("\n", '\\n', $name);

            $this->names[$flagId][$language->id->value] = $name;
        }
    }

    private function getFlagIdFromLabelName(string $labelName): int
    {
        $pattern = '/wazaclassification_(\d+)/';

        try {
            $matchResult = Regex::match($pattern, $labelName);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $flagId = $matchResult->group(1);
        } catch (RegexFailed $e) {
            echo $e->getMessage();
            exit;
        }

        return (int) $flagId;
    }

    private function exportCsv(): void
    {
        $csv = Writer::fromString();
        try {
            $csv->setOutputBOM(Bom::Utf8);
        } catch (InvalidArgument) {
            return;
        }

        $header = [''];
        foreach (array_first($this->names) as $languageId => $name) {
            $header[] = $languageId;
        }
        try {
            $csv->insertOne($header);
        } catch (Exception) {
            return;
        }

        foreach ($this->names as $flagId => $languages) {
            $names = [$flagId];
            foreach ($languages as $name) {
                $names[] = $name;
            }
            try {
                $csv->insertOne($names);
            } catch (Exception) {
                continue;
            }
        }

        try {
            $contents = $csv->toString();
        } catch (Exception) {
            return;
        }
        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/all_move_flag_names.csv",
            $contents,
        );
    }
}
