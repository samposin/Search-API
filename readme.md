# Baseify Overview
## Overview
The Purpose of the project is to create widgets which can be used as browser extensions and website widgets. These extension and widgets will suggest users with products they have been looking to purchase. So, mostly we will be targeting ecommerce website.
Client (to be installed in CH extensions, under developer's mode) - 

https://slack-files.com/T158DFRAS-F1J2SD99S-0b934d24ac 

* Development of the project is happening in two modes 
    * Frontend - where we are developing widgets
    * Backend - where we are serving specific search feed results to user

## Frontend
   There are few different types of widgets, which are calling backend services for search feeds. We are using Amazon CDN for delivery.
   
## Backend Logic
   There are so many advertisers from which we are calling search feeds, like eBay, Dealspricer, Kelkoo, Twenga, Zoom etc. There are conditions to call each advertisers, parameters like Geo, in which website user is using our widget etc. We also have a system where we define these things in the backend. We have set advertisers as Primary, First backfill and second backfill for each Geo, backfill interface. We have also defined Advertisers for each publisher level with the same logic as Geo backfill interface for Publisher FIND. You will require credentials to access backend interface. 
   * Username: pleasecontactus
   * Password: pleasecontactus

## Services
### Whitelist Domains
   There are around 60000 domains only where we are serving. We have this is separate JSON files to cross in every call.
### Domain on Domain condition
   We can not serve results from walmart on walmart.com/walmart.fr etc, we have logic for this also in our system.
   
## Internal Analytics	(Interface)
We have our internal Analytics system in place and http://admin.baseify.com/admin/analytics/daily/jsverimproving day by day
Here you can check clicks on daily and hourly basis with many filters
* High Volume websites
* Clicked Keywords data

## Release Description
We have created our own internal release description system, a basic blog pattern. Which is available at http://admin.baseify.com/admin/wiki/all 

## Reporting System
We have been generating reports on daily basis for publishers. We also creating a system so that if we don't get daily report from advertisers we can send to publishers.

* We pass subids, to each publisher from back end UI (Interface to create SubID)

## Servers
For all backend logic we are using 2 instances on AWS and one small instance for our Analytics. We are using ELB but not auto-scaling

## DB Server
We are using MySQL RDS from Amazon for this.

# Step Procedure to call APIs

* **Database download link **linkhasbeenremoved**
 
* **Get files from github into WAMP/www**
 
* Install composer from https://getcomposer.org/download/
   * Double click and install it
* Open command prompt using administrator role.
 
* **Go to directory in which project files are kept from github.**
 
* **Run this command → composer install from the command prompt.**
 
* **Open project folder in explorer**

* **Create a file named “.env” on root**
   * **File content should be:**
      * APP_ENV=local
      * APP_DEBUG=true
      * APP_KEY=iaALop71cVZ1jacChZa2OjPWA0utIWA1
      * DB_HOST=localhost
      * DB_DATABASE=dbname
      * DB_USERNAME=dbusername
      * DB_PASSWORD=dbpassword
      * CACHE_DRIVER=file
      * SESSION_DRIVER=file
      * QUEUE_DRIVER=sync
   * **Update these values in the file**
      * dbname=database name
      * dbusername=database user name
      * dbpassword=database password
      
* **Open localhost in browser**

* **Run**
   - http://localhost/projectfolder/public/api/v1/search?kw=tv&jsver=1.5.16&asin=&user=4287740423&domain=orange.fr&dealsource=%23recettes-ch&widget=Image%20Filmstrip&language=fr&resolution=1024x768&time=14:30:01&configurator_unique_id=296632c12abe3b0a7e6cedb3c7a53082&callback=___sdcb3___
   
   - In the above URL replace the variables with details as explained below:
      * Projectfolder = directory in which project files are kept from github.
      * Param kw= keyword
      * Param domain=domain on which search feed is displayed

* **If  error  “Call to undefined function openssl_decrypt()” shows up**
      * Enable this extension in your php.ini file by removing semicolon
      * extension=php_openssl.dll
      * Restart your Apache server and retry

* **If Root not found error shows up, use different URL as below:**
   http://localhost/projectfolder/public/index.php/api/v1/search?kw=tv&jsver=1.5.16&asin=&user=4287740423&domain=orange.fr&dealsource=%23recettes-ch&widget=Image%20Filmstrip&language=fr&resolution=1024x768&time=14:30:01&configurator_unique_id=296632c12abe3b0a7e6cedb3c7a53082&callback=___sdcb3___

* **If geo is FR**
   * Then open phpmyadmin
   * Open table “advertiser_publisher_search_defaults
   * Update row where id=9 with the below details
      * Set main_api=ebay_commerce_network
      * This will ensure that ebay is called when geo is france

* **Coding Style Guid**
   * We are follwing http://www.php-fig.org/psr/psr-4/ 
