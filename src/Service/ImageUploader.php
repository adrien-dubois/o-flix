<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader {

    private $slugger;

    public function __construct(SluggerInterface $slugger )
    {
        $this->slugger = $slugger;
    }

    public function upload($form, $fieldName)
    {
            /** @var UploadedFile $brochureFile */
            $imgFile = $form->get($fieldName)->getData();
            if ($imgFile) {

            $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            // We get the file name clean for safety, according the SluggerInterface Service
            $safeFilename = $this->slugger->slug($originalFilename);

            // To avoid that 2 users upload 2 files withthe same name, and to not overwrite someone's else file, we will rename our files with a uniq suffix.
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();

            // We move the file in the public asset
            try {
                $imgFile->move(
                    'uploads',
                    $newFilename
                );
                return $newFilename;
            } catch (FileException $e) {
                // If it gets wrong, we can send a mail to the admin
            }

            return false;
        }
    }
}