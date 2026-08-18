<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\Csv\Bom;
use League\Csv\Writer;

final class PokemonImporter
{
    public const string URL = 'https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/masterdata/personal.json';

    private const string COLUMN_POKEMON_ID = 'id';
    private const string COLUMN_TYPE_1_ID = 'type1';
    private const string COLUMN_TYPE_2_ID = 'type2';
    public const string COLUMN_ABILITY_1_ID = 'toku0';
    public const string COLUMN_ABILITY_2_ID = 'toku1';
    public const string COLUMN_ABILITY_3_ID = 'toku2';
    private const string COLUMN_BASE_HP = 'hp';
    private const string COLUMN_BASE_ATTACK = 'atk';
    private const string COLUMN_BASE_DEFENSE = 'def';
    private const string COLUMN_BASE_SP_ATK = 'spatk';
    private const string COLUMN_BASE_SP_DEF = 'spdef';
    private const string COLUMN_BASE_SPEED = 'agi';

    public function __construct(
        private readonly Client $client,
        private readonly PokemonRepositoryInterface $pokemonRepository,
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
            'icon',
            'sprite',
            'type1_id',
            'type2_id',
            'ability1_id',
            'ability2_id',
            'ability3_id',
            'base_hp',
            'base_atk',
            'base_def',
            'base_spa',
            'base_spd',
            'base_spe',
            'base_spc', // legacy column for gen 1 Special stat
            'egg_group1_id',
            'egg_group2_id',
            'base_experience',
            'ev_hp',
            'ev_atk',
            'ev_def',
            'ev_spa',
            'ev_spd',
            'ev_spe',
            'catch_rate',
            'base_friendship',
        ]);

        foreach ($json ?? [] as $pokemon) {
            $this->processPokemon($csv, $pokemon);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/vg_pokemon_champions.csv",
            $csv->toString(),
        );
    }

    private function processPokemon(Writer $csv, array $pokemon): void
    {
        $id = (int) ($pokemon[self::COLUMN_POKEMON_ID] ?? '');

        $pokemonId = new PokemonId($id);
        try {
            $p = $this->pokemonRepository->getById($pokemonId);
        } catch (PokemonNotFoundException $e) {
            echo $e->getMessage();
            exit;
        }
        $icon = "champions/$p->identifier.png";
        $sprite = "home/$p->identifier.png";

        $type1Id = (int) ($pokemon[self::COLUMN_TYPE_1_ID] ?? '');

        $type2Id = $pokemon[self::COLUMN_TYPE_1_ID] !== $pokemon[self::COLUMN_TYPE_2_ID]
            ? (int) ($pokemon[self::COLUMN_TYPE_2_ID] ?? '')
            : '\N'; // Porydex uses null type 2 for monotype Pokémon.

        $ability1Id = (int) ($pokemon[self::COLUMN_ABILITY_1_ID] ?? '');

        $ability2Id = $pokemon[self::COLUMN_ABILITY_1_ID] !== $pokemon[self::COLUMN_ABILITY_2_ID]
            ? (int) ($pokemon[self::COLUMN_ABILITY_2_ID] ?? '')
            : '\N'; // Porydex uses null ability 2 for single-ability Pokémon.

        $ability3Id = $pokemon[self::COLUMN_ABILITY_1_ID] !== $pokemon[self::COLUMN_ABILITY_3_ID]
            ? (int) ($pokemon[self::COLUMN_ABILITY_3_ID] ?? '')
            : '\N'; // Porydex uses null ability 3 for Pokémon without a third ability.

        $baseHp = (int) ($pokemon[self::COLUMN_BASE_HP] ?? '');
        $baseAtk = (int) ($pokemon[self::COLUMN_BASE_ATTACK] ?? '');
        $baseDef = (int) ($pokemon[self::COLUMN_BASE_DEFENSE] ?? '');
        $baseSpA = (int) ($pokemon[self::COLUMN_BASE_SP_ATK] ?? '');
        $baseSpD = (int) ($pokemon[self::COLUMN_BASE_SP_DEF] ?? '');
        $baseSpe = (int) ($pokemon[self::COLUMN_BASE_SPEED] ?? '');

        $csv->insertOne([
            VersionGroupId::CHAMPIONS,
            $id,
            $icon,
            $sprite,
            $type1Id,
            $type2Id,
            $ability1Id,
            $ability2Id,
            $ability3Id,
            $baseHp,
            $baseAtk,
            $baseDef,
            $baseSpA,
            $baseSpD,
            $baseSpe,
            0,
            '\N', // egg group 1 ID
            '\N', // egg group 2 ID
            0, // base experience
            0, // EV yield - HP
            0, // EV yield - Attack
            0, // EV yield - Defense
            0, // EV yield - Sp. Atk
            0, // EV yield - Sp. Def
            0, // EV yield - Speed
            0, // catch rate
            0, // base friendship
        ]);
    }
}
/*

id - The Pokémon's unique ID.
no - national dex number
fo - form number

ff - form type
    00 = not an alt form
    51 = Mega, Mega X
    52 = Mega Y
    ...

rr - regional form indicator
    11 = Alolan
    31 = Galarian (including Mr. Rime, Runerigus)
    41 = Hisuian (including Basculegion, Sneasler, Overqwil)
    51 = Paldean
    Unintentionally(?) left blank for Mega Meowstic (Female).

ffge - gender differences #1
    0 = the usual value
    1 = the Pokémon has visual gender differences
    Unintentionally(?) left blank for Mega Meowstic (Female).

ms_name - always "monsname_syn"
ms_name_lbl - in rom-txt/LANGUAGE/monsname_syn.json, the LabelName for this pokemon's species name
ms_form - always "zkn_form_syn"
ms_form_lbl - in rom-txt/LANGUAGE/zkn_form_syn.json, the LabelName for this pokemon's form name
ms_form_mini - always ""
ms_form_mini_lbl - always ""
disp_form - always "1"
poke_class - always "0"
is_valid - always "1"

reg_no - introduced in regulation id
1 = Regulation M-A
2 = Regulation M-B
And so on, presumably.

is_same - gender differences #2
    0 = the usual value
    1 = the Pokémon has visual gender differences
    Unintentionally(?) left blank for Mega Meowstic (Female).

cod - the id of the default form? (MOSTLY BLANK)
sex - 0 = any, 1 = always male, 2 = unknown, 3 = always female
weight - weight in kg, but the decimal should go before the final digit
weight_ms - always "zkn_weight"
weight_ms_lbl - in rom-txt/LANGUAGE/zkn_weight.json, the LabelName for this form's weight's text
merge_fo - ??? UNKNOWN

*/
