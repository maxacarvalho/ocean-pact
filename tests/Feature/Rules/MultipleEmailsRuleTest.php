<?php

test('a single email is valid', function () {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['emails' => 'email1@email.com'],
        ['emails' => [new \App\Rules\MultipleEmailsRule()]]
    );

    expect($validator->fails())->toBeFalse();
});

test('a single email is invalid', function () {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['emails' => 'email1@email'],
        ['emails' => [new \App\Rules\MultipleEmailsRule()]]
    );

    expect($validator->fails())->toBeTrue();
});

test('multiple emails are all valid', function () {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['emails' => 'email1@email.com,email2@email.com email3@email.com;email4@email.com - email5@email.com-email6@email.com'],
        ['emails' => [new \App\Rules\MultipleEmailsRule()]]
    );

    expect($validator->fails())->toBeFalse();
});

test('some emails are invalid', function () {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['emails' => 'email1@email,email2emailcom email3@email.com;email4@email.com - email5@email.com-email6@email.com'],
        ['emails' => [new \App\Rules\MultipleEmailsRule()]]
    );

    expect($validator->fails())->toBeTrue();
});
