<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Clipping;
use AppBundle\Service\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Imagick;
use Symfony\Component\HttpFoundation\File\File;

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
    
    public function __construct(FileUploader $uploader) {
        $this->uploader = $uploader;
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
            $file = new File($this->uploader->getImageDir() . '/' . $filename);
            $entity->setImageFile($file);
        }
    }
    
    private function thumbnail(Clipping $clipping) {
        $file = $clipping->getImageFile();
        $thumbname = $file->getBasename($file->getExtension()) . '_tn.jpg';
        $magick = new Imagick($file->getPathname());
        
        $magick->cropThumbnailImage(256, 171);
        $magick->setImageFormat('jpg');
        $handle = fopen($file->getPath() . '/' . $thumbname, 'wb');
        fwrite($handle, $magick->getimageblob());
        
        return $thumbname;
    }
    
    private function uploadFile(Clipping $clipping) {
        $file = $clipping->getImageFile();
        $filename = $this->uploader->upload($file);        
        $clipping->setImageFilePath($filename);            
        $clipping->setOriginalName($file->getClientOriginalName());
        $clipping->setImageSize($file->getClientSize());
        $dimensions = getimagesize($this->uploader->getImageDir() . '/' . $filename);
        $clipping->setImageWidth($dimensions[0]);
        $clipping->setImageHeight($dimensions[1]);
        
        $clippingFile = new File($this->uploader->getImageDir() . '/' . $filename);        
        $clipping->setImageFile($clippingFile);
        $clipping->setThumbnailPath($this->thumbnail($clipping));
    }
    
}
