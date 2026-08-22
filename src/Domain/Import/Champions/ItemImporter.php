<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Champions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemNotFoundException;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use League\Csv\Bom;
use League\Csv\Writer;

final class ItemImporter
{
    /** @var int[] $itemIds Indexed by item id. */
    private array $itemIds = [];

    private const string URL = 'https://raw.githubusercontent.com/projectpokemon/champout/refs/heads/main/masterdata/item.json';

    private const string COLUMN_ITEM_ID = 'id';

    public function __construct(
        private readonly Client $client,
        private readonly ItemRepositoryInterface $itemRepository,
        private readonly string $projectRoot,
    ) {}

    public function import(): void
    {
        $this->itemIds = [];

        $url = self::URL;
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

        foreach ($json ?? [] as $item) {
            $this->processItem($item);
        }
    }

    private function processItem(array $item): void
    {
        $itemId = (int) ($item[self::COLUMN_ITEM_ID] ?? '');
        $this->itemIds[$itemId] = $itemId;
    }

    private function exportCsv(): void
    {
        sort($this->itemIds);

        $csv = Writer::fromString();
        $csv->setOutputBOM(Bom::Utf8);
        $csv->insertOne([
            'version_group_id',
            'item_id',
            'game_index',
            'icon',
            'item_fling_power',
            'item_fling_effect_id',
            'is_available',
        ]);
        foreach ($this->itemIds as $itemId) {
            $id = new ItemId($itemId);
            try {
                $item = $this->itemRepository->getById($id);
            } catch (ItemNotFoundException $e) {
                echo $e->getMessage();
                exit;
            }

            $csv->insertOne([
                VersionGroupId::CHAMPIONS,
                $itemId,
                $itemId,
                "champions/$item->identifier.png",
                0,
                '\N',
                1,
            ]);
        }

        file_put_contents(
            "$this->projectRoot/ignore/champions-imports/vg_items_champions.csv",
            $csv->toString(),
        );
    }
}
/*
id - item id
category_a - ???
category_b - ???
category_c - ???
limit - ???
ms_name - always "itemname"
ms_lbl - in rom-txt/LANGUAGE/itemname.json, the LabelName for this item's name
ms_name_info - always "iteminfo_syn"
ms_lbl_info - in rom-txt/LANGUAGE/iteminfo_syn.json, the LabelName for this item's description
unlock - ???
sort - ???
vp_conversion - how much VP this item will be converted into if a player gets an extra
con_ref - ???
buf_ref - ???
*/
