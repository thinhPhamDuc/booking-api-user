<?php

use App\Enums\Constant;
use Illuminate\Support\Str;

function generateToken() {
    return substr(Str::random(Constant::STRING_RANDOM_TOKEN), 0, 30) . strtotime("now");
}

function typeFile($file) {
    return explode('/',$file)[1];
}