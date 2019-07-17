# Convert Images to Webp
Search for images in subdirectories recursively, will create the same structure in **Output** folder  
By default images will be converted in four compressions(25, 50, 75, 100).
For Example.:
> Root directory- `files/pictures/`  
Input Directory- `files/pictures/images`  
Output Directory- `converted/`  

Output Will be.:  
`converted/25/images/`  
`converted/50/images/`  
`converted/75/images/`  
`converted/100/images/`  

## Requirements
This package requires [Intervention Image Package](http://image.intervention.io/)  
> Run `composer install`
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

