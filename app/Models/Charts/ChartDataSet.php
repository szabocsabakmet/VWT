<?php

namespace App\Models\Charts;

class ChartDataSet
{
    public string $label;
    public array $data;
    public string $backgroundColor;
    public string $borderColor;

    public function __construct(string $label, array $data, string $backgroundColor, string $borderColor)
    {
        $this->label = $label;
        $this->data = $data;
        $this->backgroundColor = $backgroundColor;
        $this->borderColor = $borderColor;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    /**
     * @return string
     */
    public function getBorderColor(): string
    {
        return $this->borderColor;
    }
}
