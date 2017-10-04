<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of FileUploader
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class FileUploader {
    
    /**
     * @var string
     */
    private $imageDir;
    
    public function __construct($imageDir) {
        $this->imageDir = $imageDir;
    }

    /**
     * @param \AppBundle\Service\UploadedFile $file
     */
    public function upload(UploadedFile $file) {
        $filename = md5(uniqid()) . '.' . $file->guessExtension();            
        $file->move($this->imageDir, $filename);   
        return $filename;
    }

    /**
     * @return string
     */
    public function getImageDir() {
        return $this->imageDir;
    }
}
