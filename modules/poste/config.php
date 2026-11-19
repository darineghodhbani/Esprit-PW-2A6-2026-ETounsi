<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Models/PosteRepository.php';
require_once __DIR__ . '/securite.php';

function posteRepository()
{
    return new PosteRepository(Config::getConnexion());
}

function e($valeur)
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
