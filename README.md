# WebInstagram
Assignment 1 of CSCI4140 @ 2019 Sem 2

# Link to the application
https://mighty-river-79307.herokuapp.com/

# Folder structure
```
|-- root | 
    |-- .gitignore 
    |-- composer.json
    |-- composer.lock
    |-- Procfile
    |-- src
        |-- editor.php - The main program of photo editor
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
        |-- sections
            |-- admin.php - UI and business logic of the administration area 
            |-- album.php - UI and business logic of the album on index
            |-- member.php - UI and business logic of the member area, handling login & logout
            |-- upload.php - UI of the upload form located in index page
```


# Dev procedure
I am using the postgreDB as my DB package along with amazon S3 for storaging all the images. All the filters and image manipulation are powered by Imagick for PHP.

# Request for bonus points
## 1. Design pattern
I spent a bit more time on researching a better development pattern in PHP to archieve better code readibility, maintainability and more extendable codebase. I try to make things more organize by grouping some of the main functionality to together as a class like Image Service, User Service, and DB Helper. So that I will have a single file to handle a  specific functionality.

To make the code even more readable. I try to separate all the strings and constant value into their own file. Again one file for each of them. And it can prevent me from putting a non-exist value on things like DB query and arguments in functions.

Moreover, I try to leverage the native template engine in PHP by again separating different parts of UI and business logic into files. So even there are many pieces of stuff going on in the index file. I can still split them into manageable chunks(admin.php for the admin area, member.php for the login and logout, etc) to take care of. 

## 2. Session management
Instead of a simple cookie only authentication system. I spent time on developing a more mature session management system. I added a sessions table in DB, each session record comes with a expiry time. Whenever a user is accessing the application, the UserService instance will check if it's a expired session. If so then the session record will be deactived and user is forced to login again. 

The cookie will only store a session token instead of any user information. So that it's a more secure way to handle user's personal data. Also session token are hashed with sha256, so attackers are not able to obtain a session token by brute force.