<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;

final class MoveImporter
{
    private const string URL = 'https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/masterdata/waza.json';

    private const string COLUMN_MOVE_ID = 'id';
    private const string COLUMN_TYPE_ID = 'type';
    private const string COLUMN_CATEGORY_ID = 'category';
    private const string COLUMN_TARGET_ID = 'target';
    private const string COLUMN_POWER = 'power';
    private const string COLUMN_ACCURACY = 'accuracy';
    private const string COLUMN_PP = 'pp';
    private const string COLUMN_PRIORITY = 'priority';
    private const string COLUMN_CAN_USE = 'available';

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
            'can_use_move',
            'type_id',
            'quality_id',
            'category_id',
            'power',
            'accuracy',
            'pp',
            'priority',
            'min_hits',
            'max_hits',
            'infliction_id',
            'infliction_percent',
            'duration_id',
            'min_turns',
            'max_turns',
            'crit_stage',
            'flinch_percent',
            'effect',
            'recoil_percent',
            'heal_percent',
            'target_id',
            'affinity_id',
            'max_move_id',
            'max_power',
            'z_move_id',
            'z_base_power',
            'z_power_effect_id',
            'effect_percent',
        ]);

        foreach ($json ?? [] as $move) {
            $this->processMove($csv, $move);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/vg_moves_champions.csv",
            $csv->toString(),
        );
    }

    private function processMove(Writer $csv, array $move): void
    {
        $moveId = (int) ($move[self::COLUMN_MOVE_ID] ?? '');
        $canUseMove = (int) ($move[self::COLUMN_CAN_USE] ?? '');
        $typeId = (int) ($move[self::COLUMN_TYPE_ID] ?? '');
        $categoryId = (int) ($move[self::COLUMN_CATEGORY_ID] ?? '');
        $power = (int) ($move[self::COLUMN_POWER] ?? '');
        $accuracy = (int) ($move[self::COLUMN_ACCURACY] ?? '');
        $pp = (int) ($move[self::COLUMN_PP] ?? '');
        $priority = (int) ($move[self::COLUMN_PRIORITY] ?? '');
        $targetId = (int) ($move[self::COLUMN_TARGET_ID] ?? '');

        $csv->insertOne([
            VersionGroupId::CHAMPIONS,
            $moveId,
            $canUseMove,
            $typeId,
            '\N', // quality id
            $categoryId,
            $power,
            $accuracy,
            $pp,
            $priority,
            0, // min hits
            0, // max hits
            '\N', // infliction id
            0, // infliction percent
            '\N', // duration id
            0, // min turns
            0, // max turns
            0, // crit stage
            0, // flinch percent
            0, // effect
            0, // recoil percent
            0, // heal percent
            $targetId,
            '\N', // affinity_id
            '\N', // max_move_id
            '\N', // max_power
            '\N', // z_move_id
            '\N', // z_base_power
            '\N', // z_power_effect_id
            '\N', // effect_percent
        ]);
    }
}
/*
id - move id
type - type id
category - category id
target - target id
power - power
accuracy - accuracy
pp - pp
direct - ???
priority - priority
classification_a - ???
classification_b - ???
text_pattern - ???
tcost - ???
available - can use move
ms_name - always "wazaname"
ms_lbl - in rom-txt/LANGUAGE/wazaname.json, the LabelName for this move's name
ms_name_info - always "wazainfo_syn"
ms_lbl_info - in rom-txt/LANGUAGE/wazainfo_syn.json, the LabelName for this move's description
con_ref - ???
buf_ref - ???
*/
