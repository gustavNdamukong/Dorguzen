![issues](https://img.shields.io/github/issues/gustavNdamukong/Dorguzen)
![forks](https://img.shields.io/github/forks/gustavNdamukong/Dorguzen)
![stars](https://img.shields.io/github/stars/gustavNdamukong/Dorguzen)
![license](https://img.shields.io/github/license/gustavNdamukong/Dorguzen)

#  DORGUZEN
 A PHP MVC development framework. Your Rapid Web Development Toolkit

## INSTALLATION AND USAGE
###   Using Composer
    Get your first 'Hello world' page up in Dorguzen in minutes. Here is how:
    -Go via the terminal into your server root where you have all your web projects and after initialising Composer in that folder (composer init)
    run the following command:

        ```composer create-project nolimitmedia/dorguzen```

        It will create a folder called 'dorguzen' and install the Dorguzen framework for you in it.

###   Clone or download from GitHub
    -Alternatively you can download or clone the framework code directly from GitHub onto your your server root folder and have all the code at your disposal,
      reigns in your hands, ready to fire away and start building your stunning new application :)

    -The name of the application that it has started you off with is called Dorguzen, so you need to change that to the name
        of the app you are building. In fact, you need to make a few configuration changes to get started.

    -Configure your new application by going through the following steps:

        i) Start by changing the name of the root folder from 'dorguzen' to yourAppName
        ii) navigate to configs/config.php which is Dorguzen's global configuration file. Go through this file and change every instance of
            'dorguzen' to yourAppName. All the settings in this config file are pretty self-explanatory and are well commented so you know what to put under
            each group and why. It contains things like appUrl, appBusinessName, localUrl, liveUrl, localDBCredentials,
            liveDBCredentials etc etc. You will be using this file a lot, but the most important settings to get you up and running are the layout directory
            and layout class file and the DB connection stuff.

            -It is important that the 'appName' key exactly matches the root directory name of your application, otherwise you will get errors of your layout not 
                being found.
            -It is also important that the 'fileRootPathLocal' key is given the exact same value as the 'appName' key, which is the name of your application's 
                root directory.

            -Also, Dorguzen needs to know the layout directory path in order to route your view files properly.
        iii) Dorguzen comes with a database file to start you off with a database. This file is called 'dorguzApp.sql'. Run it in your database client software
                and it will create a database called 'dorguzapp'. The user credentials that Dorguzen expects to use to access this database are the following:

                    Username: 'dorguz'
                    Password: 'dorguz123'
                    Database: 'dorguzapp'

            These connection credentials are already registered in the configs/config.php file so you don't have to do anything apart from run the query to create the
            database and tables and then use your database client tool, for example phpMyAdmin to create a user 'dorguz' and password 'dorguz123' for the database
            'dorguzapp'. Obviously, you can name your database something else and use a different username and password to access it. Just make sure you update these 
            credentials under the 'SET the local/live DB connection credentials' section in configs/config.php to match them. 
            Once that is done, you can get rid of the dorguzApp.sql file.

            -You may want to go into layouts folder and change the name of the directory 'dorguzApp' to 'yourAppName'. This layouts directory is where all your       
                application themes will live. Each theme (layout) would have its own separate directory in here, and you would set one of them as the default 
                layout to be used in configs/config.php like this:

                    'layoutDirectory' => 'dorguzApp',     
				    'defaultLayout' => 'dorguzAppLayout',

             -If you change the values of the above (layoutDirectory, and defaultLayout), you should go into that layout's subdirectory and also change the name of   
                the layout file inside of it, say from 'dorguzAppLayout.php' to 'yourAppNameLayout.php'.
             -If you changed the name of the layout directory, do not forget to go change the namespaces of files in there to
                reflect the new namespace.
                -There are two files to change namespaces in; 'BlankLayout.php' and 'yourAppNameLayout.php'. Go into these and change their namespaces at the from

                    'namespace layouts\dorguzApp;' to 'namespace namespace layouts\yourAppName;'

                -Remember that this is because you changed the name of their parent layout directory to 'yourAppName'.

        -Finally, you can test to see the Dorguzen welcome page in the browser by typing in your browser the URI of that folder on your server.

                    http://localhost/myAppName/

            -Remember you can change your database credentials to whatever you want and enter the new ones into config/config.php


 ## Admin authentication (login)
    -Dorguzen comes with a login feature and one super-admin user set up for you with the following login details:

                    Username: 'dorguzen@dorguzen.com'
                    Password: 'dorguzen'

    -The email is fake so you should change the login details to something more secure once you log in.
    -Once logged in, you have access to a dashboard where you can make a couple of management changes to your application. This is meant to give you and idea
        of how you could go about building a cool content management system. From this dashboard you are able to change your password, create other users who
        can log into the system. You can view contact messages sent through your application's contact form. These are messages that would have been emailed
        to you but are also stored in a database table for your perusal in the admin interface from where you can delete them at your convenience.
    -As a logged in user, you are also able to request to reset your password in case you forgot it.
