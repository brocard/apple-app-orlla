<?php
set_time_limit(0);

require 'functions.php';
require 'config.php';
require 'app_ids.php';

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$i = 1;
foreach ($app_ids as $id => $count) {
    $url = "http://itunes.apple.com/lookup?id=$id";
    $app_detail = get_site_content($url); 
    $app_detail = json_decode($app_detail);
    if ($app_detail->resultCount == 0) {
        unset($app_ids[$id]);
        continue;
    }

    $app_detail = $app_detail->results;
    $app_detail = $app_detail[0];

    $kind = $app_detail->kind; 
    unset($app_detail->kind);

    $description = mysql_escape_string($app_detail->description); 
    unset($app_detail->description);

    $screenshotUrls = json_encode($app_detail->screenshotUrls); 
    unset($app_detail->screenshotUrls);

    $genres= json_encode($app_detail->genres); 
    unset($app_detail->genres);

    $genreIds= json_encode($app_detail->genreIds); 
    unset($app_detail->genreIds);

    $features = json_encode($app_detail->features); 
    unset($app_detail->features);

    $supportedDevices = json_encode($app_detail->supportedDevices); 
    unset($app_detail->supportedDevices);

    $ipadScreenshotUrls = json_encode($app_detail->ipadScreenshotUrls); 
    unset($app_detail->ipadScreenshotUrls);
    
    $languageCodesISO2A = json_encode($app_detail->languageCodesISO2A); 
    unset($app_detail->languageCodesISO2A);

    $isGameCenterEnabled = $app_detail->isGameCenterEnabled             ; unset($app_detail->isGameCenterEnabled) ;
    $artworkUrl60        = $app_detail->artworkUrl60                    ; unset($app_detail->artworkUrl60)        ;
    $artworkUrl512       = $app_detail->artworkUrl512                   ; unset($app_detail->artworkUrl512)       ;
    $artistViewUrl       = $app_detail->artistViewUrl                   ; unset($app_detail->artistViewUrl)       ;
    $artistId            = $app_detail->artistId                        ; unset($app_detail->artistId)            ;
    $artistName          = mysql_escape_string($app_detail->artistName) ; unset($app_detail->artistName)          ;
    $price               = $app_detail->price                           ; unset($app_detail->price)               ;

    $version               = $app_detail->version                                ; unset($app_detail->version              ) ;
    $releaseDate           = $app_detail->releaseDate                            ; unset($app_detail->releaseDate          ) ;
    $sellerName            = mysql_escape_string($app_detail->sellerName)        ; unset($app_detail->sellerName           ) ;
    $currency              = $app_detail->currency                               ; unset($app_detail->currency             ) ;
    $bundleId              = $app_detail->bundleId                               ; unset($app_detail->bundleId             ) ;
    $trackId               = $app_detail->trackId                                ; unset($app_detail->trackId              ) ;
    $trackName             = mysql_escape_string($app_detail->trackName)         ; unset($app_detail->trackName            ) ;
    $primaryGenreName      = $app_detail->primaryGenreName                       ; unset($app_detail->primaryGenreName     ) ;
    $primaryGenreId        = $app_detail->primaryGenreId                         ; unset($app_detail->primaryGenreId       ) ;
    $wrapperType           = $app_detail->wrapperType                            ; unset($app_detail->wrapperType          ) ;
    $trackCensoredName     = mysql_escape_string($app_detail->trackCensoredName) ; unset($app_detail->trackCensoredName    ) ;
    $trackViewUrl          = $app_detail->trackViewUrl                           ; unset($app_detail->trackViewUrl         ) ;
    $contentAdvisoryRating = $app_detail->contentAdvisoryRating                  ; unset($app_detail->contentAdvisoryRating) ;
    $artworkUrl100         = $app_detail->artworkUrl100                          ; unset($app_detail->artworkUrl100        ) ;
    $fileSizeBytes         = $app_detail->fileSizeBytes                          ; unset($app_detail->fileSizeBytes        ) ;
    $sellerUrl             = $app_detail->sellerUrl                              ; unset($app_detail->sellerUrl            ) ;
    $formattedPrice        = $app_detail->formattedPrice                         ; unset($app_detail->formattedPrice       ) ;
    $trackContentRating    = $app_detail->trackContentRating                     ; unset($app_detail->trackContentRating   ) ;

    $releaseNotes                       = mysql_escape_string($app_detail->releaseNotes)  ; unset($app_detail->releaseNotes                         ) ;
    $averageUserRatingForCurrentVersion = $app_detail->averageUserRatingForCurrentVersion ; unset($app_detail->averageUserRatingForCurrentVersion   ) ;
    $userRatingCountForCurrentVersion   = $app_detail->userRatingCountForCurrentVersion   ; unset($app_detail->userRatingCountForCurrentVersion     ) ;
    $averageUserRating                  = $app_detail->averageUserRating                  ; unset($app_detail->averageUserRating                    ) ;
    $userRatingCount                    = $app_detail->userRatingCount                    ; unset($app_detail->userRatingCount                      ) ;

    $sql = "INSERT INTO app ( releaseNotes, averageUserRatingForCurrentVersion, userRatingCountForCurrentVersion, averageUserRating, userRatingCount, version, releaseDate, sellerName, currency, bundleId, trackId, trackName, primaryGenreName, primaryGenreId, wrapperType, trackCensoredName, trackViewUrl, contentAdvisoryRating, artworkUrl100, fileSizeBytes, sellerUrl, formattedPrice, trackContentRating, artistName, artistId, artistViewUrl, artworkUrl512, isGameCenterEnabled, artworkUrl60, price, languageCodesISO2A, ipadScreenshotUrls, supportedDevices, features, genreIds, genres, kind, screenshotUrls, description) 
        VALUES ( '$releaseNotes', '$averageUserRatingForCurrentVersion', '$userRatingCountForCurrentVersion', '$averageUserRating', '$userRatingCount', '$version', '$releaseDate', '$sellerName', '$currency', '$bundleId', '$trackId', '$trackName', '$primaryGenreName', '$primaryGenreId', '$wrapperType', '$trackCensoredName', '$trackViewUrl', '$contentAdvisoryRating', '$artworkUrl100', $fileSizeBytes, '$sellerUrl', '$formattedPrice', '$trackContentRating', '$artistName', '$artistId', '$artistViewUrl', '$artworkUrl512', '$isGameCenterEnabled', '$artworkUrl60', price, '$languageCodesISO2A', '$ipadScreenshotUrls', '$supportedDevices', '$features', '$genreIds', '$genres', '$kind', '$screenshotUrls', '$description')";
    //echo $sql;

    if ( ! mysql_query($sql, $conn)) {
        echo $id . ' insert failure'."\n";
        //echo $sql;
        //exit;
    } else {
        unset($app_ids[$id]);
        echo $i++ . '   ';
        file_put_contents('app_ids.php', '<?php $app_ids = '. var_export($app_ids, TRUE) . ';');
    } 
}

echo "finished!\n";


//end  file 
/*
[trackId] => 333211132
[kind] => software
[artistViewUrl] => https://itunes.apple.com/us/artist/neat-touch/id325929579?uo=4
[artworkUrl60] => http://a1553.phobos.apple.com/us/r1000/060/Purple/34/e3/ab/mzl.dalpgtfb.png
[isGameCenterEnabled] => 
[artworkUrl512] => http://a265.phobos.apple.com/us/r1000/098/Purple/7b/ae/9f/mzl.vyawkxwh.png
[artistId] => 325929579
[artistName] => Neat Touch
[price] => 0
[version] => 1.0.0
[description] => Mayhem in Teddy 
[releaseDate] => 2009-10-20T11:54:54Z
[sellerName] => Neat Services Ltd
[currency] => USD
[bundleId] => com.neattouch.runpandarun
[trackViewUrl] => https://itunes.apple.com/us/app/!run-teddy-run!-warning-contains/id333211132?mt=8&uo=4
[trackName] => !Run Teddy Run! - Warning contains decapitated teddies
[primaryGenreName] => Games
[primaryGenreId] => 6014
[wrapperType] => software
[trackCensoredName] => !Run Teddy Run! - Warning contains decapitated teddies
[contentAdvisoryRating] => 9+
[artworkUrl100] => http://a265.phobos.apple.com/us/r1000/098/Purple/7b/ae/9f/mzl.vyawkxwh.png
[fileSizeBytes] => 7338487
[sellerUrl] => http://www.neattouch.com
[formattedPrice] => Free
[averageUserRatingForCurrentVersion] => 3
[userRatingCountForCurrentVersion] => 225
[trackContentRating] => 9+
[averageUserRating] => 3
[userRatingCount] => 225


[features] => Array
[supportedDevices] => Array
[screenshotUrls] => Array
[ipadScreenshotUrls] => Array
[genreIds] => Array
[genres] => Array
[languageCodesISO2A] => Array
*/
