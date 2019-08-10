# WebInstagram

## Link to the application
https://mighty-river-79307.herokuapp.com/

## Folder structure
```
|-- root | 
    |-- .gitignore 
    |-- composer.json
    |-- composer.lock
    |-- Procfile - commands for my local dev envionment 
    |-- src - source code
        |-- editor.php - The UI and business logic of photo editor
        |-- index.php - The entry page of the application
        |-- assets - for all the static files like stylesheets & images
        |   |-- lensflare.png - The lens flare image using in one of the filters
        |-- consts - all the constant variable using in the apps globally
        |   |-- constants.php - General constants
        |   |-- messages.php - Informative messages used across the apps 
        |-- lib - custom created library 
        |   |-- base.php - one file to require all the necessary library
        |   |-- config.php - global configuaration like DB and apps settings
        |   |-- dal.class.php - Database access layer, handling the DB connection and all the DB query helper functions
        |   |-- image.class.php - The Image Service class, handling all the stuffs relate to image including uploading image, applying filters, etc 
        |   |-- user.class.php - The User Service class, handling user login, logout, check for valid session etc
        |-- sections - separating different sections of UI & logic as a single file
            |-- admin.php - UI and business logic of the administration area 
            |-- album.php - UI and business logic of the album on index
            |-- member.php - UI and business logic of the member area, handling login & logout
            |-- upload.php - UI of the upload form located in index page
```


## Dev procedure
I am using the postgreDB as my DB package along with amazon S3 for storaging all the images. All the filters and image manipulation are powered by Imagick for PHP.
