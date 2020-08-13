![issues](https://img.shields.io/github/issues/gustavNdamukong/Dorguzen)
![forks](https://img.shields.io/github/forks/gustavNdamukong/Dorguzen)
![stars](https://img.shields.io/github/stars/gustavNdamukong/Dorguzen)
![license](https://img.shields.io/github/license/gustavNdamukong/Dorguzen)

##  DORGUZEN
 A PHP MVC development framework. Your Rapid Web Development Toolkit

## INSTALLATION AND USAGE
    Get your first 'Hello world' page up in Dorguzen in minutes. Here is how:
    -Go via the terminal into your server root where you have all your web projects and run this command
        ```composer create-project nolimitmedia/dorguzen```
        It will create a folder called 'dorguzen' and install the Dorguzen framework for you in it.
    -The name of the application that it has started you off with is called Dorguzen, so you need to change the name to the name
        of the app you are building.
    -To do so, follow the following steps:
        i) Start by changing the name of the root folder from 'dorguzen' to yourAppName
        ii) navigate to settings/Settings.php (a config class file as you could probably guess) and from top to bottom change every instance of
            'dorguzen' to yourAppName. All the settings in this file are pretty self-explanatory and are well commented so you will know what to put under each
            group just by looking at the notes in there. It contains things like appUrl, appBusinessName, localUrl, liveUrl, localDBCredentials, liveDBCredentials
            etc etc. You will be using this file a lot, bt the most important to get you up and running are the layout directory and layout class file and
            the DB connection stuff
            Dorguzen needs to know the layout directory path in order to route your view files properly.
        iii) Dorguzen comes with a database file to start you of with a database. This file is called 'dorguzApp.sql'. Run it in your database client software
            and it will create a database called 'dorguzApp' with a user already set up to access this database with the following credentials:

                    Username: 'dorguz'
                    Password: 'dorguz123'
                    Database: 'dorguzApp'

            These connection credentials are already registered in the Settings.php file so you don't have to do anything apart from run the query to create the
            database and tables. Once that is done you can get rid of the dorguzApp.sql file

            -In the Settings.php change the value of the 'layoutDirectory' key to 'yourAppName' and the value of 'defaultLayout' to something like 'yourAppNameLayout'.
             It will look like so:

                    ```'layoutDirectory' => 'yourAppName',```
                     ```'defaultLayout' => 'yourAppNameLayout',```

             -Go into layouts and change the name of the directory 'dorguzApp' to 'yourAppName'
             -Go into the directory and also change the name of the layout file 'dorguzAppLayout.php' to 'yourAppNameLayout.php'.
             -Because you changed the name of the layout directory, do not forget to go into this directory and change the namespace of the two files in there to
                reflect the new namespace.
                -These two files are 'BlankLayout.php' and 'yourAppNameLayout.php'. Go into these files and change their namespaces at the top from
                    namespace layouts\dorguzApp; to namespace layouts\yourAppName;

                -Remember that this is because you changed the name of their parent layout directory to 'yourAppName'.

        -Finally, you can test to see the Dorguzen welcome page in the browser by typing in your browser the URI of that folder on your server. Mine looks somethings
            this:

                    ```localhost:8888/myAppName/

            -You can change your database credentials if you want and enter the new ones into Settings.php


 # ADMIN AUTHENTICATION (Login)
    -Dorguzen comes with a login feature and one super-admin user set up for you with the following login details:

                    Username: 'doguzen@doguzen.com'
                    Password: 'dorguzen'

    -The email is fake so you should change the login details to something more secure once you log in.
    -Once logged in, you have access to a dashboard where you can make a couple of management changes to your application. This is meant to give you and idea
        of how you could go about building a cool content management system. From this dashboard you are able to change your password, create other users who
        can log into the system. You can view contact messages sent through your application's contact form. These are messages that would have been emailed
        to you but are also stored in a database table for your perusal in the admin interface from where you can delete them at your convenience.
    -As a logged in user, you are also able to request to reset your password in case you forgot it.


 # DORGUZEN DOCUMENTATION

    A more official and thorough documentation is being compiled and will soon be made available on the parent platform Nolimit Media, and also here on GitHub.
    Please bare with me. In the mean time if you have any questions, reach out to me here on here and i will get right back at you.