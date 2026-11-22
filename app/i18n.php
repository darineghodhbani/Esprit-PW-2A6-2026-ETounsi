<?php

const ET_LANGS = ['fr', 'ar'];

if (empty($_SESSION['lang']) || !in_array($_SESSION['lang'], ET_LANGS, true)) {
    $_SESSION['lang'] = 'fr';
}

function et_lang(): string {
    return $_SESSION['lang'];
}

function et_dir(): string {
    return et_lang() === 'ar' ? 'rtl' : 'ltr';
}

function et_messages(): array {
    static $messages = null;
    if ($messages === null) {
        $messages = require __DIR__ . '/lang/' . et_lang() . '.php';
    }
    return $messages;
}

function t(string $key): string {
    return et_messages()[$key] ?? $key;
}

function m(array $item, string $field): string {
    $value = $item[$field] ?? '';
    if (is_array($value)) {
        return (string) ($value[et_lang()] ?? $value['fr'] ?? reset($value));
    }
    return (string) $value;
}

function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
