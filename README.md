#Guayaquil v2.0
*Read the documentation for details: [wiki.technologytrade.ru](http://wiki.technologytrade.ru)*

**Requirements:**

* PHP 5.6 +
* php-soap
* php-xml
* php-zip
* php-openssl
* php-mbstring
* php-curl

###How to install?
**Demo:**
> 1. Place the files in a directory accessible to the web server.
> 2. Run "php composer.phar install" in library directory.
> 3. Use index.php like entry point to show demo.

###How to  use lib?
> 1. import GuayaquilRequestOEM or GuayaquilRequestAM classes.
> 2. Use instance of GuayaquilRequestOEM or GuayaquilRequestAM to create request. Add requests by "append" methods.

**Find by VIN example:**
    
    $catalogCode = 'AU1221';
    $vin = 'WAUZZZ4M0HD042149';
    
    $request = new GuayaquilRequestOEM($catalogCode, '', 'en_US');
    $request->appendFindVehicleByVIN($vin);
    $data = $request->query(); /** Now you can see VehicleListObject in $data[0] */

**Get catalog info example:**

    $catalogCode = 'AU1221';
    $request = new GuayaquilRequestOEM($catalogCode, '', 'en_US');
    $request->appendGetCatalogInfo();
    $data = $request->query(); /** Now you can see CatalogObject in $data[0] */
  
**Multiple requests (You can use up to five at a time):**
    
    $catalogCode = 'AU1221';
    $vin = 'WAUZZZ4M0HD042149';
    $request = new GuayaquilRequestOEM($catalogCode, '', 'en_US');
    $request->appendGetCatalogInfo();
    $request->appendFindVehicleByVIN($vin);
    $data = $request->query(); /** Now you can see CatalogObject in $data[0] and VehicleListObject in $data[1] */
