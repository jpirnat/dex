<?php
declare(strict_types=1);

use Jp\Trendalyzer\Repositories\AbilitiesRepository;
use Jp\Trendalyzer\Repositories\ItemsRepository;
use Jp\Trendalyzer\Repositories\MovesRepository;
use Jp\Trendalyzer\Repositories\NaturesRepository;
use Jp\Trendalyzer\Repositories\PokemonRepository;

$dice = new \Dice\Dice();


// PDO
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$rule = [
	'constructParams' => [
		"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
		$user,
		$pass,
	],
	'shared' => true,
	'call' => [
		['setAttribute', [PDO::ATTR_EMULATE_PREPARES, false]],
		['setAttribute', [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]],
	],
];
$dice->addRule(PDO::class, $rule);


// Repositories

// AbilitiesRepository
$rule = [
	'shared' => true,
];
$dice->addRule(AbilitiesRepository::class, $rule);

// ItemsRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ItemsRepository::class, $rule);

// MovesRepository
$rule = [
	'shared' => true,
];
$dice->addRule(MovesRepository::class, $rule);

// NaturesRepository
$rule = [
	'shared' => true,
];
$dice->addRule(NaturesRepository::class, $rule);

// PokemonRepository
$rule = [
	'shared' => true,
];
$dice->addRule(PokemonRepository::class, $rule);


return $dice;
