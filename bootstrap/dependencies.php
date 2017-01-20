<?php
declare(strict_types=1);

use Jp\Dex\Stats\Repositories\ShowdownAbilityRepository;
use Jp\Dex\Stats\Repositories\ShowdownItemRepository;
use Jp\Dex\Stats\Repositories\ShowdownMoveRepository;
use Jp\Dex\Stats\Repositories\ShowdownNatureRepository;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;

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

// ShowdownAbilityRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ShowdownAbilityRepository::class, $rule);

// ShowdownItemRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ShowdownItemRepository::class, $rule);

// ShowdownMoveRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ShowdownMoveRepository::class, $rule);

// ShowdownNatureRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ShowdownNatureRepository::class, $rule);

// ShowdownPokemonRepository
$rule = [
	'shared' => true,
];
$dice->addRule(ShowdownPokemonRepository::class, $rule);


return $dice;
