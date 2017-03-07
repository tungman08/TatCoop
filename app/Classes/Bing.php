<?php

namespace App\Classes;

use App\Background;
use DB;
use Diamond;
use Storage;
use GuzzleHttp\Client;

class Bing {

    // Constants
    const LIMIT_N = 1; // Bing's API returns at most 8 images
    const LOCALE = 'en-US';
    const RESOLUTION_LOW = '1366x768';
    const RESOLUTION_HIGH = '1920x1080';

    // API
    const BASE_URL = 'http://www.bing.com';
    const JSON_URL = '/HPImageArchive.aspx?format=js';

    private $resolution;

    public function __construct() {
        $this->setResolution(self::RESOLUTION_HIGH);
    }

    public function photo($d=0) {
        $date = ($d < 4) ? $d : 4;
        $database = Diamond::parse(Background::max('background_date'));

        return Background::whereDate('background_date', '=', $database->addDays(-1 * $date))->first();
    }

    public function download() { 
        $image = $this->fetchImages(0);
        $result = false;

        if ($image !== false) {
            $database = (Background::count() > 0) ? Diamond::parse(Background::max('background_date')) : Diamond::minValue();
            $newest = Diamond::createFromFormat('Ymd', $image->startdate);
            $expire = Diamond::createFromFormat('Ymd', $image->startdate)->addDays(-5);
            $result = 0;

            if ($newest->diffInDays($database) > 0) {
                $this->delete($expire);
                $result = $this->save($newest->diffInDays($database));
            }
        }

        return $result;
    }

    protected function delete($expire) {
        $backgrounds = Background::whereDate('background_date', '<=', $expire)->get();

        foreach ($backgrounds as $background) {
            $filename = Diamond::parse($background->background_date)->format('Ymd') . '.jpg';
            Storage::disk('backgrounds')->delete($filename);
        }

        Background::whereDate('background_date', '<=', $expire)->delete();       
    }

    protected function save($n) {
        $loop = ($n < 4) ? $n : 5;
        $count = 0;

        for ($i = 0; $i < $loop; $i++) {
            $image = $this->fetchImages($i);

            if ($image !== false) {
                DB::transaction(function() use ($image) {
                    $url = $this->setQuality($image->url);
                    $filename = $image->startdate . '.jpg';

                    Storage::disk('backgrounds')->put($filename, file_get_contents($url));

                    $background = new Background();
                    $background->background_date = Diamond::createFromFormat('Ymd', $image->startdate);
                    $background->url = $url;
                    $background->copyright = $image->copyright;
                    $background->copyrightlink= $image->copyrightlink;
                    $background->save();
                });

                $count++;
            }
        }

        return $count;
    }

    private function fetchImages($d) {
        // Constructing API url
        $url = self::BASE_URL . self::JSON_URL .
            '&idx=' . $d .
            '&n=' . self::LIMIT_N .
            '&mkt=' . self::LOCALE;

        $result = $this->fetchJSON($url);

        return ($result !== false) ? $result->images[0] : $result;
    }

    /**
	 * Fetches the image JSON data from Bing
     * @return array Associative data array
     */
    private function fetchJSON($url, $count=0) {
        if ($count < 3) {
            $client = new Client();
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody());
            }
            else {
                sleep(3);
                $this->fetchJSON($url, ++$count);
            }
        }
        else {
            return false;
        }
    }

    /**
     * Returns the class resolution
     * @return array Class resolution
     */
    public function getResolution() {
        return $this->resolution;
    }

    /**
     * Sets the class resolution
     * @param array $args
     */
    public function setResolution($resolution) {
        $this->resolution = $resolution;
        $this->sanityCheck();

        return $this;
    }

    /**
     * Performs some sanity checks
     * @return array Validated resolution
     * @internal param array $args Class resolution
     */
    private function sanityCheck() {
        if (in_array($this->resolution, [self::RESOLUTION_LOW, self::RESOLUTION_HIGH]) === false) {
             $this->resolution = self::RESOLUTION_HIGH;
        }
    }

    /**
     * Sets the image resolution
     * @param array $images Array with image data
     * @return array Modified image data array
     */
    private function setQuality($url) {
        return self::BASE_URL . str_replace(self::RESOLUTION_HIGH, $this->resolution, $url);
    }
}