<?php

namespace Lemec93\Support\Exporters;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Lemec93\Support\Interfaces\ExporterInterface;

abstract class Exporter implements FromQuery, WithHeadings, WithMapping, ExporterInterface
{
    use Exportable;

    protected $query;
    protected $fileName;
    protected $type = 'csv';

    public function setQuery($query): self
    {
        $this->query = $query;

        return $this;
    }

    public function query()
    {
        return $this->query;
    }

    public function setFileName($fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param $type string should be one of presented here https://docs.laravel-excel.com/3.0/exports/export-formats.html
     *
     * @return $this
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function export(): string
    {
        $filename = $this->getFileName();

        $this->store($filename, null, ucfirst($this->type));

        return Storage::path($filename);
    }

    public function headings(): array
    {
        return is_associative($this->getFields()) ? array_keys($this->getFields()) : $this->getFields();
    }

    public function map($row): array
    {
        return array_map(function ($fieldName) use ($row) {
            return Arr::get($row, $fieldName);
        }, $this->getFields());
    }

    abstract public function getFields(): array;

    protected function getFileName(): string
    {
        $this->fileName = empty($this->fileName) ? uniqid() : $this->fileName;

        return $this->fileName . '.' . $this->type;
    }
}
