<?php


namespace App;

interface IUrlChecker
{
    public function getBrokenImages(): array;

    public function getAllLinks(): array;
}