<?php
final class HomeController
{
    public function index(): void
    {
        $title = "EcoRide";
        require __DIR__ . '/../../views/home.php';
    }
}
