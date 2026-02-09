<?php
// src/Controllers/StaticController.php

final class StaticController
{
    public function contact(): void
    {
        $title = "Contact";
        $viewFile = __DIR__ . '/../../views/static/contact.php';
        require __DIR__ . '/../../views/layout.php';
    }

    public function mentionsLegales(): void
    {
        $title = "Mentions légales";
        $viewFile = __DIR__ . '/../../views/static/mentions_legales.php';
        require __DIR__ . '/../../views/layout.php';
    }
}
