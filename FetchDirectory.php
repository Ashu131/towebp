<?php
// namespace App;

class FetchDirectory
{
    /**
     * This function will alter array values
     * by "concate" function
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
}