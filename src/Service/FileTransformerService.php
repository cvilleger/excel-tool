<?php

namespace App\Service;

use App\Entity\User;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\SheetInterface;

class FileTransformerService
{
    public function convert(User $user)
    {
        $files = $user->getFiles();
        foreach ($files as $file) {
            $reader = ReaderEntityFactory::createReaderFromFile($file->getPath());
            $reader->open($file->getPath());//4
            /** @var SheetInterface $sheet */
            foreach ($reader->getSheetIterator() as $sheet) {
                /** @var Row $row */
                foreach ($sheet->getRowIterator() as $row) {
                    if ($sheet->getRowIterator()->key() < 4) {
                        continue;
                    }

                    $data = $row->toArray();

                    $id = $data[1];
                    $email = $data[2];
                    $civility = $data[3];
                    $name = $data[4];
                    $firstname = $data[5];
                    $address1 = $data[6];
                    $address2 = $data[7];
                    $postalCode = $data[8];
                    $city = $data[9];
                    $phone = $data[10];
                    $optInEmail = $data[11];
                    $cartPrice = $data[12];
                    $fidCard = $data[13];
                    $lastShopDate = $data[14];

                    // TODO
                }
            }

        }
    }
}