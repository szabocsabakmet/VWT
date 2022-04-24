<?php

namespace App\Services;

use Illuminate\Http\Request;

interface AlgorithmHandler
{
    public function __construct(Request $request);
    public function validateRequest(): void;
    public function hasErrors(): bool;
    public function run(): void;
}
