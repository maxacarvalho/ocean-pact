<?php

test('can validate cron expression', function (string $expression, bool $expected) {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['expression' => $expression],
        ['expression' => new \App\Rules\CronExpressionRule()]
    );
    expect($validator->fails())->toBe($expected);
})->with([
    ['* * * * *', false],
    ['*/5 * * * *', false],
    ['* * * * * *', true],
    ['*/5 24 * * *', true],
]);
