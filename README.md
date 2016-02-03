<center><img src="img/logo.png" width="250px"></center><br/>

**IMPORTANT: This code is no longer maintained! It relies on many API calls which may or may not work. This codebase is archival and not intended for production use!**

Welcome to eDart
----------------
eDart was an online marketplace for college students to trade the textbooks, notebooks, and electronic devices they no longer needed for the ones they do. Users would post items, which would then receive item offers on their dedicated pages. Users could accept the most appealing offer then make the transaction. The site was online from the fall of 2013 to the fall of 2014, and was accessible at http://wewanttotrade.com.

Running eDart
-------------
As stated, ***this code is not intended for production use***. However, there is no reason you can't still run the server to poke around. If you are feeling adventurous enough, here's how you do it (as taken from the official eDart README c. August 2014):

### Setting Up A PHP Server

If this is your first time PHP-ing, you’ll need the correct environment for testing. It is recommended you either download MAMP (Mac, [http:// www.mamp.info](http:// www.mamp.info)) or WAMP (Windows, http://www.wampserver.com) to get started. If you are on Linux, you already have the tools to set up your own LAMP server. For more information regarding this, please visit http://lamphowto.com. It is also suggested that you read heavily into the documentation for the software package before attempting to use it. Once you think you’ve got the hang of it, point the web server to the `edart` subdirectory of your newly cloned repo. Also make sure that Apache is running on port **80** and MySQL is running on **3306**. If all this information is correct, you should be good to go.

There is still one very important step. In the eDart source home directory, you will see a file with no extension called simply `.htaccess`. If you cannot see this file, you will have to enable “View Hidden Files” on your computer. The method varies per operating system, so you may have to research the procedure unique to your OS. Once you can see the file, clicking it will most likely open a text editor with its contents. If not, select a text editor on your computer to view the file. In the file, you will see a line that begins:

    SetEnv DOC_ROOT

Followed by a dummy path wrapped in quotation marks. Leaving the quotation marks in-tact, replace the file path with the file path of your source code directory (include the `/edart` subdirectory in your file path). Save the file and then close the file. You are now ready to proceed.

### Setting Up MySQL for Local Testing

Woah, slow down Mr. Eager Beaver! If you notice, simply navigating to localhost in your web browser may either result in a blank page or a ton of errors. This occurs because you are missing a vital piece of eDart: its database. The database is the heart of the site… it is required to make everything function, including the main function. Fortunately, setting up MySQL for your own local eDart testing is not all that hard.
To begin, open up your new eDart repository location and navigate to        `/setup`. There you should find a file named `setup.sql`. Copy this script to your desktop for ease of use. Now, open up a MySQL command window **while the MySQL server is running**. This may vary depending on your MySQL setup, so be sure to consult the proper documentation before attempting this step. Once you are in the command prompt, type the following command, where `path/to/sql/script` is the desktop path to the script you just copied:

    source path/to/sql/script

If the script completes successfully, your new database should be populated with tables reflecting those on the eDart server and you should have a new MySQL user named ‘edart’. You may visit localhost to validate. To log in, use the credentials below:

    Email: developer@edart.edu
    Password: dev2014

Understanding The Code
----------------------
Also found in eDart's official README was a breakdown of what can be found in each directory in the codebase. That breakdown is as follows:

- **about** Page describing the background of the site
- **abuse** Page allowing users to report abusive items
- **api** The primary REST API
    - **api_lib** Contains supporting files for the main API
- **bugs** Page allowing users to report site bugs
- **changes** The changelog of the site
- **contact** Page with contact information for the site
- **files** Various supporting files for the site
    - **fonts** Web fonts for use by @font-face
- **forgot** Page allowing users to reset their password
- **imageviewer** Prints an item image via a GET request
- **img** Stores every image on the site
    - **icon** All icons used on the site
- **lib** Every third-party library we use
- **me** The user’s main profile
    - **picture** The user’s profile picture
- **private** Any files for developer use
- **scripts** Every script on the site
    - **php** All PHP scripts
        - **ajax** Any PHP script that will be strictly called by jQuery Ajax
        - **cron** All scripts that will be strictly run as cron tasks
        - **method** PHP scripts that involve functions
        - **class** All PHP classes
            - **widget** PHP “widgets” that are printed onto certain pages
            - **element** Core elements of the infrastructure (e.g. users, items, exchanges, messages, etc.)
    - **css** All CSS stylesheets to be included by min
        - **mob** Any mobile stylesheets (excluded from min)
    - **js** All JavaScript on the site
- **signup** Includes all scripts involving signup, including email validation
- **video** A web player for the eDart instructional video

FAQ
---

##### _"You talk an awful lot about this "official README". Why didn't you just include it in the repo?"_

As sexy as the old README was (just believe me, it was sexy), it included sensitive server information and credentials designed specifically for people who wanted to work on the project full time. My friend hosted the site for me, and seeing how the site is no longer active, I'm withholding the README so y'all don't go and try hacking my friend. Plus, who knows what other info I threw into that README. I don't need you hacking me, either.

##### _"This some of the worst code I've ever read. What the hell were you thinking?"_

Keep in mind that this was written by a kid (A.K.A. me) during his freshman year of college. With almost no formal training in the world of PHP, I set out to write a website from the ground up that, by the end of its development, had becomes its own barebones MVC framework. In retrospect, I had no idea what I was doing. However, looking back, it's pretty cool to be able to say I did it all from scratch. Hopefully you can understand my messy code.

#### _"Can I use this for a thing?"_
Depends on what. Shoot me a private message if you wish to use this code in some (commercial) way. #kthx
