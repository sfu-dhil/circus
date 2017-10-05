<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Clipping;
use AppBundle\Service\FileUploader;
use AppBundle\Service\Thumbnailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of ClippingListener
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class ClippingListener {

    /**
     * @var FileUploader
     */
    private $uploader;
    
    /**
     * @var Thumbnailer
     */
    private $thumbnailer;
    
    public function __construct(FileUploader $uploader, Thumbnailer $thumbnailer) {
        $this->uploader = $uploader;
        $this->thumbnailer = $thumbnailer;
    }
    
    public function setThumbWidth($width) {
        $this->thumbWidth = $width;
    }
    
    public function setThumbHeight($height) {
        $this->thumbHeight = $height;
    }
    
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof Clipping) {
            $this->uploadFile($entity);
        }
    }
    
    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof Clipping) {
            $this->uploadFile($entity);
        }
    }
    
    public function postLoad(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof Clipping) {
            $filename = $entity->getImageFilePath();
            if(file_exists($this->uploader->getImageDir() . '/' . $filename)) {
                $file = new File($this->uploader->getImageDir() . '/' . $filename);
                $entity->setImageFile($file);
            }
        }
    }
    
    private function uploadFile(Clipping $clipping) {
        $file = $clipping->getImageFile();
        if( ! $file instanceof UploadedFile) {
            return;
        }
        $filename = $this->uploader->upload($file);        
        $clipping->setImageFilePath($filename);            
        $clipping->setOriginalName($file->getClientOriginalName());
        $clipping->setImageSize($file->getClientSize());
        $dimensions = getimagesize($this->uploader->getImageDir() . '/' . $filename);
        $clipping->setImageWidth($dimensions[0]);
        $clipping->setImageHeight($dimensions[1]);
        
        $clippingFile = new File($this->uploader->getImageDir() . '/' . $filename);        
        $clipping->setImageFile($clippingFile);
        $clipping->setThumbnailPath($this->thumbnailer->thumbnail($clipping));
    }
    
}
