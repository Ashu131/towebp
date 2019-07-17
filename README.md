# Convert Images to Webp
## How to convert images to webp

Initiate `AutoConverter.php` Class  
> `$convert= new AutoConverter($root_directory, $image_directory, $output_directory, false);`  

Class `AutoConverter.php` Initiation require three parameters, fourth parameter is `true` by default
- Root Directory
- Image Directory(without forward slash `folder/pictures`)
- Output Directory
- Boolean(default:`true`)
    - `true` to convert new files only
    - `false` to convert all files

Call `convert()` method on the class Instance  
> `$convert->convert();`

