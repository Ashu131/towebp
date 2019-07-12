<?php
require 'vendor/autoload.php';
use Intervention\Image\ImageManagerStatic as Image;

class Convert  
{
    /**
     * Pass second parameter for quality ex. -q75
     */
    private $imagedir;
    private $outputdir;
    private $quality=75;

    public function __construct($inputdir, $outputdir) {
        $this->imagedir = $inputdir;
        $this->outputdir = $outputdir;
        $this->setImageQuality();
    }

    public function convert()
    {
        $imageAr= $this->getImageArrayFromDirectory();
        die('ksdjh');
        try {
            foreach ($imageAr as $value) {
                //encode images to webp
                $image = Image::make($value)->encode('webp', $this->quality);
                //Filename without Extension
                $filename= pathinfo($value, PATHINFO_FILENAME);
                //Filename to Store
                $filenameToStore= $filename.'.webp';
                $image->save("$this->outputdir$filenameToStore", $this->quality);
                
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }

    private function setImageQuality()
    {
        $num = getopt("q:");
        if ($num !== false && isset($num['q'])) {
            $this->quality = (int)$num['q'];
        }
    }

    private function getImageArrayFromDirectory()
    {
        $images = [];
        foreach (glob("$this->imagedir*.{jpeg,jpg,gif,tif,png,bmp}", GLOB_BRACE) as $file) {
            $images[] = $file;
        }
        return $images;
    }
}


$image_directory= 'pictures/all/';  $output_directory= 'pictures/converted';

$convert= new Convert($image_directory, $output_directory);
$convert->convert();

/**
 * Task-------
 * Loop through input directories
 * create directory if not found on output directory
 */

