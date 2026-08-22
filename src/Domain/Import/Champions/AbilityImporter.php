<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;

final class AbilityImporter
{
    /** @var int[] $abilityIds Indexed by ability id. */
    private array $abilityIds = [];

    public function __construct(
        private readonly Client $client,
        private readonly string $projectRoot,
    ) {}

    public function import(): void
    {
        $this->abilityIds = [];

        $url = PokemonImporter::URL;
        $this->processUrl($url);

        $this->exportCsv();
    }

    private function processUrl(string $url): void
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (GuzzleException $e) {
            echo $e->getMessage();
            exit;
        }

        $html = $response->getBody()->getContents();

        $json = json_decode($html, true);

        foreach ($json ?? [] as $pokemon) {
            $this->processPokemon($pokemon);
        }
    }

    private function processPokemon(array $pokemon): void
    {
        $abilityId = (int) ($pokemon[PokemonImporter::COLUMN_ABILITY_1_ID] ?? '');
        $this->abilityIds[$abilityId] = $abilityId;

        $abilityId = (int) ($pokemon[PokemonImporter::COLUMN_ABILITY_2_ID] ?? '');
        $this->abilityIds[$abilityId] = $abilityId;

        $abilityId = (int) ($pokemon[PokemonImporter::COLUMN_ABILITY_3_ID] ?? '');
        $this->abilityIds[$abilityId] = $abilityId;
    }

    private function exportCsv(): void
    {
        sort($this->abilityIds);

        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'version_group_id',
            'ability_id',
        ]);
        foreach ($this->abilityIds as $abilityId) {
            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $abilityId,
            ]);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/vg_abilities_champions.csv",
            $csv->toString(),
        );
    }
}
