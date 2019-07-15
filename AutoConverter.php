<?php
// namespace App;

require 'vendor/autoload.php';
use Intervention\Image\ImageManagerStatic as Image;
// use Directory;
require_once 'FetchDirectory.php';

class AutoConverter  extends FetchDirectory
{
    /**
     * Pass second parameter for quality ex. -q75
     */
    private $inputdir;
    private $outputdir;
    private $quality=75;
    private $image_directory_array=[];
    const IMAGE_QUALITIES = ['25', '50', '75', '100'];

    public function __construct($inputdir, $outputdir) {
        $this->setImageQuality();
        $this->inputdir = $inputdir;
        $this->outputdir = $outputdir;
        $this->image_directory_array = $this->subDirectoriesArray($this->inputdir);
    }

    public function convert()
    {
        foreach ($this->image_directory_array as $dir) {
            $imageAr= $this->getImageArrayFromDirectory($dir);
            try {
                foreach ($imageAr as $value) {
                    //encode images to webp
                    $image = Image::make($value)->encode('webp');
                    //Filename without Extension
                    $filename= pathinfo($value, PATHINFO_FILENAME);
                    //Filename to Store
                    $filenameToStore= $filename.'.webp';

                    foreach (self::IMAGE_QUALITIES as $image_quality) {
                        $this->quality = (int)$image_quality;
                        $path_to_save="$this->outputdir$image_quality/$dir";
                        
                        if (!file_exists($path_to_save)) {
                            mkdir($path_to_save, 666, true);
                        }
                        
                        $image->save("$this->outputdir$image_quality/$dir$filenameToStore", $this->quality);
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    private function setImageQuality()
    {
        $num = getopt("q:");
        if ($num !== false && isset($num['q'])) {
            $this->quality = (int)$num['q'];
        }
    }

    private function getImageArrayFromDirectory($dir)
    {
        $images = [];
        foreach (glob("$dir*.{jpeg,jpg,gif,tif,png,bmp}", GLOB_BRACE) as $file) {
            $images[] = $file;
        }
        return $images;
    }
}

/**
 * Don't append forward slash to $image_directory Array.
 */
$image_directory= 'pictures/all';  $output_directory= 'pictures/converted/';

$convert= new AutoConverter($image_directory, $output_directory);
$convert->convert();

/**
 * Task-------
 * Loop through input directories---------------------------
 * create directory if not found on output directory--------
 * Save files in each directory-----------------------------
 * 25,50,75,100---------------------------------------------
 * Check for new files
 * convert only them
 */

