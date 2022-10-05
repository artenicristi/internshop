<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    public function __construct(
        private readonly ImageOptimizer $imageOptimizer,
        private readonly SluggerInterface $slugger,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $this->slugger->slug($originalFilename).'.'.$file->guessClientExtension();

        /** @var string $rootDir */
        $rootDir = $this->parameterBag->get('kernel.project_dir');
        $originalDirectory = $rootDir.'/public/images/original';
        $modifiedDirectory = $rootDir.'/public/images/modified';

        $file->move(
            $originalDirectory,
            $filename
        );

        $this->imageOptimizer->resize($originalDirectory, $modifiedDirectory, $filename);

        return $filename;
    }
}
