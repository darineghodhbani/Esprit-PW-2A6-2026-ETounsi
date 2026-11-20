<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function invalidClass(array $errors, string $field): string
{
    return isset($errors[$field]) ? ' is-invalid is-invalid-custom' : '';
}

function errorFor(array $errors, string $field): string
{
    if (!isset($errors[$field])) {
        return '';
    }
    return '<div class="invalid-feedback d-block form-feedback-custom invalid-custom server-error">'
        . e($errors[$field]) . '</div>';
}

function logoHtml(string $height = '45px', string $fallbackIcon = '<i class="fa fa-landmark me-3"></i>'): string
{
    if (is_file(__DIR__ . '/../img/logo.png')) {
        return '<img src="img/logo.png" alt="Logo E-Tounsi" style="height: ' . e($height) . '; width: auto;" class="me-2">';
    }
    return $fallbackIcon;
}
