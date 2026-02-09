<?php
final class HomeController
{
    public function index(): void
    {
        $title = "EcoRide";
        $viewFile = __DIR__ . '/../../views/home.php';
        require __DIR__ . '/../../views/layout.php';
    }
}
