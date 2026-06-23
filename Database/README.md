

## Setup before you can use the database.

# Step 1: Install Xampp (on the assumption that everyones laptops run Windows)
Go to [https://apachefriends.org](https://www.apachefriends.org/download.html) and click on the third download link, which should be the most recent version.

Run the downloaded installer, just keep clicking next and wait until it installs. 

When it installs, you can choose to have the control panel open immediately or not. I suggest unchecking the box, so that you don't have to close it again and reopen it with administrative rights.

# Step 2: Download the zipped repository file and move the unzipped folder into the Xampp htdocs file. 

On the main page of this repository, press the dropdown on the green code button, and press download Zip.

When the Xampp app installed, it should have created a folder within your Local disk called 'xampp'. Within that folder, there should be a folder called htdocs. Please extract the downloaded zip file to that htdocs folder. 

# Step 3: Open Xampp again with administrative access

Return to the main Xampp file, and scroll down until you find the .exe file called xampp-control. Right click that file, and click run as administrator. Click yes on the popup. Xampp should open within a moment. Please ensure that the services 'Apache' and 'MySql' are started by pressing start for both under the Actions heading. No other services are needed. 

# Step 4: Open phpmyadmin on your browser

Type in localhost/phpmyadmin on your browser. It should open the dashboard.

At the top, there should be a list of buttons/tabs. Click on the one that says import and press select file. Navigate to the xampp file, then to htdocs, then to the Bona-Marets folder, and then lastly to the Database folder. Click on the schema.sql file to import it. You should, after that, have full access to the tables, however, besides the categories, there will be no other seeded data. You can create more through signups, etc, but some parts might not work because it doesnt have code. :)

# Step 5: Open the actual code

As long as you ensured the entire file and all the code from the repository is inside the htdocs file, you can actually open up the website on your laptop.

It should be something like this, typed into your browser and then the html/css, or whateer was on the respository at that time, will be there: localhost/bona-markets/public 


## If you have any trouble with any of this, please let me know and we can have a teams meeting and I can show you how to fix any errors, or how to set it up.

---
