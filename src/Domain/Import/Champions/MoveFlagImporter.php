<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\Moves\Flags\MoveFlagId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;

final class MoveFlagImporter
{
    private const string URL = 'https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/masterdata/waza.json';

    private const string COLUMN_MOVE_ID = 'id';
    private const string COLUMN_CONTACT = 'direct';
    private const string COLUMN_FLAG_1 = 'classification_a';
    private const string COLUMN_FLAG_2 = 'classification_b';

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
            'move_id',
            'flag_id',
        ]);

        foreach ($json ?? [] as $move) {
            $this->processMove($csv, $move);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/vg_moves_flags_champions.csv",
            $csv->toString(),
        );
    }

    private function processMove(Writer $csv, array $move): void
    {
        $moveId = (int) ($move[self::COLUMN_MOVE_ID] ?? '');
        $contact = (int) ($move[self::COLUMN_CONTACT] ?? '0');
        $championsFlag1Id = (int) ($move[self::COLUMN_FLAG_1] ?? '0');
        $championsFlag2Id = (int) ($move[self::COLUMN_FLAG_2] ?? '0');

        $porydexFlag1Id = $this->toPorydexFlagId($championsFlag1Id);
        $porydexFlag2Id = $this->toPorydexFlagId($championsFlag2Id);

        if ($contact) {
            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $moveId,
                MoveFlagId::CONTACT,
            ]);
        }
        if ($porydexFlag1Id) {
            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $moveId,
                $porydexFlag1Id,
            ]);
        }
        if ($porydexFlag2Id) {
            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $moveId,
                $porydexFlag2Id,
            ]);
        }
    }

    private function toPorydexFlagId(int $championsFlagId): int
    {
        return match ($championsFlagId) {
            0 => 0,
            1 => MoveFlagId::PUNCHING,
            2 => MoveFlagId::SOUND_BASED,
            3 => MoveFlagId::DANCE,
            4 => MoveFlagId::SLICING,
            5 => MoveFlagId::WIND,
            6 => MoveFlagId::POWDER,
            7 => MoveFlagId::BALL_AND_BOMB,
            8 => MoveFlagId::PULSE,
            9 => MoveFlagId::BITING,
            10 => MoveFlagId::EXPLOSIVE,
            11 => MoveFlagId::MENTAL,
            12 => MoveFlagId::HEALING,
            default => throw new Exception("Unknown move flag id: $championsFlagId"),
        };
    }
}
/*
id - move id
direct - ???
classification_a - ???
classification_b - ???
con_ref - ???
buf_ref - ???
*/
