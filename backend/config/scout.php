<?php
return [
    "driver" => env("SCOUT_DRIVER", "elastic"),
    "prefix" => env("SCOUT_PREFIX", "ricette_"),
    "queue" => env("SCOUT_QUEUE", false),
    "chunk" => [
        "searchable" => 500,
        "unsearchable" => 500,
    ],
    "soft_delete" => false,
    "identify" => env("SCOUT_IDENTIFY", false),
];
