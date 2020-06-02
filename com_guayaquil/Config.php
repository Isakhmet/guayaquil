<?php

namespace guayaquil;

if (Config::$dev) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

class Config
{
    const baseDir = __DIR__;
    const imageSize = 250;
    public static $dev = true;
    public static $ui_localization = 'ru'; public static $catalog_data = 'ru_RU';
    public static $useLoginAuthorizationMethod = true;
    public static $vehiclesMaxField = 20;
    public static $defaultUserLogin = '';
    public static $defaultUserKey = '';

    /* ws.oem web-service url */
    public static $oemServiceUrl = 'ws.laximo.net';

    /* aftermarket service url */
    public static $amServiceUrl = 'aws.laximo.net';

    /* get login, key, backurl from environment */
    public static $useEnvParams = false;

    /* show start page. Catalogs list will shown if false */
    public static $showWelcomePage = true;

    /* show demo to guest */
    public static $showToGuest = true;

    /* show request text and response xml-message */
    public static $showRequest = true;

    /* show quick-groups tree to guest */
    public static $showGroupsToGuest = true;

    /* show oem-numbers on unit page, quick-details page and in xml-response message */
    public static $showOemsToGuest = true;

    /* show find by oem field, find all detail usage in modification and details-list in modification */
    public static $showApplicability = true;

    /* Url to page where you can see offers to current detail */
    public static $SiteDomain = 'http://site.com/index.php?keyword={article}&brand={brand}';

    /* image placeholder */
    public static $imagePlaceholder = 'com_guayaquil/assets/images/no-image.gif';

    /* columns on catalogs list page */
    public static $catalogColumns = 3;

    /* be shown if no example in response data */
    public static $defaultVin = 'KMHVD34N8VU263043';

    /* be shown if no example in response data */
    public static $defaultFrame = 'XZU423-0001026';

    /* added big letters to catalog names, so you can find your catalog easier */
    public static $showCatalogsLetters = true;

    public static $useWebserviceAuthorize = false;

    /* system find named css-file and apply it, now can be "guayaquil", "green" */
    public static $theme = 'guayaquil';
    public static $backurlError = 'index.php?task=error&type=backurl';
    public static $linkTarget = '_parent';

    public static $VehiclesColumns = [
        'brand',
        'name',
        'date',
        'datefrom',
        'dateto',
        'model',
        'framecolor',
        'trimcolor',
        'modification',
        'grade',
        'frame',
        'engine',
        'engineno',
        'transmission',
        'doors',
        'manufactured',
        'options',
        'creationregion',
        'destinationregion',
        'description'
    ];

    public static $groupVehicles = true;
    public static $toolbarPages = [];
}
