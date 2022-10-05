<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private const MAX_WIDTH = 550;
    private const MAX_HEIGHT = 500;

    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $originalDirectory, string $modifiedDirectory, string $filename): void
    {
        [$width, $height] = getimagesize($originalDirectory.DIRECTORY_SEPARATOR.$filename);
        $ratio = $width / $height;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($originalDirectory.DIRECTORY_SEPARATOR.$filename);
        $photo->resize(new Box($width, $height))->save($modifiedDirectory.DIRECTORY_SEPARATOR.$filename);
    }
}
