<?php

namespace App\Service;

use App\Entity\User;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\SheetInterface;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class FileTransformerService
{
    private $uploadDir;
    private $reader;
    private $writer;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
        $this->reader = ReaderEntityFactory::createXLSXReader();
        $this->writer = WriterEntityFactory::createCSVWriter();
    }

    public function convert(User $user): ?string
    {
        $files = $user->getFiles();
        if (empty($files)) {
            return null;
        }

        $this->writer->openToFile($this->uploadDir.'/test.csv');

        foreach ($files as $file) {
            $this->reader->open($file->getPath());
            /** @var SheetInterface $sheet */
            foreach ($this->reader->getSheetIterator() as $sheet) {
                /** @var Row $row */
                foreach ($sheet->getRowIterator() as $row) {
                    // TODO start Iterator at 4 (config/const)
                    if ($sheet->getRowIterator()->key() < 4) {
                        continue;
                    }

                    $this->writer->addRow(WriterEntityFactory::createRowFromArray($row->toArray()));
                }
            }

        }

        $this->writer->close();

        return $this->uploadDir.'/test.csv';
    }
}