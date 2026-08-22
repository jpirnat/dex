<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;

final class PokemonMoveImporter
{
    private const string URL = 'https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/masterdata/waza_learn.json';

    private const string COLUMN_POKEMON_ID = 'id';
    private const string COLUMN_MOVE_IDS = 'waza';

    public function __construct(
        private readonly Client $client,
        private readonly string $projectRoot,
    ) {}

    public function import(): void
    {
        $url = self::URL;
        $this->processUrl($url);
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

        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'version_group_id',
            'pokemon_id',
            'move_id',
            'move_method_id',
            'level',
            'mastery_level',
            'sort',
        ]);

        foreach ($json ?? [] as $pokemon) {
            $this->processPokemon($csv, $pokemon);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/pokemon_moves_champions.csv",
            $csv->toString(),
        );
    }

    private function processPokemon(Writer $csv, array $pokemon): void
    {
        $pokemonId = (int) ($pokemon[self::COLUMN_POKEMON_ID] ?? '');

        $moveIds = (string) ($pokemon[self::COLUMN_MOVE_IDS] ?? '');
        $moveIds = explode(',', $moveIds);

        foreach ($moveIds as $moveId) {
            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $pokemonId,
                $moveId,
                MoveMethodId::TUTOR,
                0, // level
                0, // mastery level
                0, // sort
            ]);
        }
    }
}
