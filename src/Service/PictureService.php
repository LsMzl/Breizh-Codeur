<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService {

    // Permet d'aller chercher les informations présentes dans la section parameters de services.yaml
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Permet de faire un ajout d'image et de la redimensionner
     * @param UploadedFile $picture Image uploadée
     * @param ?string $folder Dossier dans lequel est stockée l'image
     * @param int $width Largeur de l'image, par défaut 250
     * @param int $height Hauteur de l'image, par défaut 250
     */
    public function addPicture(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // Stockage des images en webp
        $file = md5(uniqid(rand(), true)) . '.webp';

        // Récupération des informations de l'image (largeur, hauteur ...)
        $pictureInfos = getimagesize($picture);

        if($pictureInfos === false) {
            throw new Exception('Format d\'image non valide');
        }

        // Vérification du format de l'image
        switch($pictureInfos['mime']){
            case 'image/png':
                $picture_source = \imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $picture_source = \imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image non valide');
        }

        // Recadrement de l'image
        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        // Vérification de l'orientation de l'image
        switch($imageWidth <=> $imageHeight){
            case -1: // Si largeur < hauteur => portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 0: // Si largeur = hauteur => carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 1: // Si largeur = hauteur => paysage
                $squareSize = $imageWidth;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;   
        }

        // Création d'une nouvelle image
        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $resized_picture, 
            $picture_source,
            0, 
            0, 
            $src_x, 
            $src_y, 
            $width, 
            $height,
            $squareSize,
            $squareSize
        );

        $path = $this->params->get('images_directory') . $folder;

        // Création du dossier de destination s'il n'existe pas
        if(!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        // Stockage de l'image recadrée
        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $file );

        $picture->move($path . '/', $file);

        return $file;
    }

    /**
     * Permet de supprimer une image
     * @param string $fichier Image à supprimer
     * @param ?string $folder Dossier dans lequel est stockée l'image
     * @param int $width Largeur de l'image, par défaut 250
     * @param int $height Hauteur de l'image, par défaut 250
     */
    public function deletePicture(string $fichier, ?string $folder ='', ?int $width = 250, ?int $height = 250 ) 
    {
        
        if($fichier !== 'default.webp') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            // Permet de savoir quel fichier est à supprimer
            $miniature = $path . '/mini/'. $width . 'x' . $height . '-' . $fichier;

            if(file_exists($miniature)){
                unlink($miniature);
                $success = true;
            }

            $original = $path . '/mini/' . $fichier;

            if(file_exists($original)){
                unlink($miniature);
                $success = true;
            }

            return $success;
        }
        return false;

    }
}