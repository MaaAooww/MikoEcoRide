<?php
// src/Controllers/StaticController.php

final class StaticController
{
    public function contact(): void
    {
        require __DIR__ . '/../../views/static/contact.php';
    }

    public function mentionsLegales(): void
    {
        require __DIR__ . '/../../views/static/mentions_legales.php';
    }
}
