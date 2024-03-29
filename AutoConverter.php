<?php

require 'vendor/autoload.php';
use Intervention\Image\ImageManagerStatic as Image;

class AutoConverter 
{
    /**
     * Pass second parameter for quality ex. -q75
     */
    private $rootdir;
    private $inputdir;
    private $outputdir;
    private $quality=75;
    private $image_directory_array=[];
    private $remove_directory_array = [];

    const IMAGE_QUALITIES = ['25', '50', '75', '100'];

    private $convert_new_file_status = true;

    public function __construct($rootdir, $inputdir, $outputdir,array $remove_directory_array, $convert_new_file_status=true) {
        $this->setImageQuality();
        $this->rootdir                  = $rootdir;
        $this->inputdir                 = $inputdir;
        $this->outputdir                = $outputdir;
        $this->remove_directory_array   = $remove_directory_array;
        $this->convert_new_file_status  = $convert_new_file_status;
        $this->image_directory_array    = $this->subDirectoriesArray($this->inputdir);
        // remove some directories to be converted
        $this->filterDirectoryArray();
    }

    /**
     * Set Image via CLI on second parameter ex. php AutoConverter -q25
     */
    private function setImageQuality()
    {
        $num = getopt("q:");
        if ($num !== false && isset($num['q'])) {
            $this->quality = (int)$num['q'];
        }
    }

    /**
     * This function will alter array values
     * by "concate" function
     * @param $dir
     */
    public function subDirectoriesArray($dir= 'pictures/all')
    {
        $subDirArr = $this->getSubDirectories($dir);
        array_walk($subDirArr,array($this,"concate"));
        
        return $subDirArr;
    }
    
    private function concate(&$value,$key)
    {
        $value.="/";
    }

    /**
     * @param $dir
     * @return array | $subDir
     */
    public function getSubDirectories($dir)
    {
        $subDir = array();
        $directories = array_filter(glob($dir), 'is_dir');
        $subDir = array_merge($subDir, $directories);
        foreach ($directories as $directory){
            $subDir = array_merge($subDir, $this->getSubDirectories($directory.'/*'));
        }
        return $subDir;
    }

    /**
     * Unset some directory array values form Directories array
     */
    private function filterDirectoryArray()
    {
        $keys_to_unset = $this->array_find($this->remove_directory_array, $this->image_directory_array);
        foreach ($keys_to_unset as $key) {
            unset($this->image_directory_array[$key]);
        }
    }

    /**
     * @param $values_to_remove | $main_array
     * @return array $keys_to_unset
     */
    private function array_find(array $values_to_remove, array $main_array)
    {
        $keys_to_unset = [];
        foreach ($values_to_remove as $needle) {
            foreach ($main_array as $key => $value) {
                if (stripos($value, $needle) !== false) { 
                    $keys_to_unset[] = $key;
                }
            }
        }
        return $keys_to_unset;
    }

    public function convert()
    {
        foreach ($this->image_directory_array as $dir) {
            $imageArray     = $this->getImageArrayFromDirectory($dir);
            $newFilesArray  = $this->getNewFiles($imageArray);
            $img_save_directory = str_replace($this->rootdir,'',$dir);
            
            try {
                foreach ($newFilesArray as $value) {
                    //encode images to webp
                    $image = Image::make($value)->encode('webp');
                    //Filename without Extension
                    $filename= pathinfo($value, PATHINFO_FILENAME);
                    //Filename to Store
                    $filenameToStore= $filename.'.webp';

                    foreach (self::IMAGE_QUALITIES as $image_quality) {
                        $this->quality  = (int)$image_quality;
                        $path_to_save   = "$this->outputdir$image_quality/$img_save_directory";
                        
                        if (!file_exists($path_to_save)) {
                            mkdir($path_to_save, 666, true);
                        }

                        $image->save("$this->outputdir$image_quality/$img_save_directory$filenameToStore", $this->quality);
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * @param $dir
     * @return array | $images
     */
    private function getImageArrayFromDirectory($dir)
    {
        $images = [];
        foreach (glob("$dir*.{jpeg,jpg,gif,tif,png,bmp}", GLOB_BRACE) as $file) {
            $images[] = $file;
        }
        return $images;
    }

    /**
     * @param array | $allImageArray
     * @return $newFilesArray OR $allImageArray
     */
    private function getNewFiles(array $allImageArray)
    {
        if ($this->convert_new_file_status) {
            $newFilesArray = array_filter($allImageArray, function ($file) {
                return filemtime($file) >= strtotime("-1 days");
            });
            return $newFilesArray;
        }
        return $allImageArray;
    }

}

/**
 * Don't append forward slash to $image_directory Array.
 * Parameters of class instantiation
 * First    => Image Directory(without forward slash)
 * Second   => Ouptput Directory
 * Third    => List of directories which don't need to be converted
 * Fourth   => Default(true) for new image converting only
 */
$root_directory = '/var/www/html/ashutosh/modules/imagetowebp/pictures';
$image_directory= $root_directory.'/all';  $output_directory= $root_directory.'/new/';
$remove_directory = ['/var/www/html/ashutosh/modules/imagetowebp/pictures/all/first/parent/','second'];

$convert= new AutoConverter($root_directory, $image_directory, $output_directory, $remove_directory, false);
$convert->convert();
