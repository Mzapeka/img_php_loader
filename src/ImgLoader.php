<?php

namespace Mzapeka\ImgLoader;

use InvalidArgumentException;
use Mzapeka\ImgLoader\Exceptions\ImgLoaderException;
use RuntimeException;

/**
 * Class ImgLoader
 *
 * The main class for API consumption
 *
 * @package Mzapeka\Imgloader
 */
class ImgLoader
{

    const IMG_TYPES = ['png', 'gif', 'jpg'];

    public $url = null;

    public $baseUrl = null;

    public $imageLinksArray = null;

    public $picFolder = null;

    public $pageContent = null;

    /**
     * Setup path to folder where you intend save pictures
     * @param string $picFolder <p>
     * String with path to existing folder
     * </p>
     */
    public function setPicFolder($picFolder)
    {
        if (!is_dir($picFolder)) {
            throw new InvalidArgumentException('Folder ' . $picFolder . ' not found. Please check the path');
        }
        $this->picFolder = $picFolder;
    }

    /**
     * Setup URL of remote host from which you intend download images
     * @param string $url <p>
     * The string with URL like http://test.com/test/../..
     * </p>
     * @throws InvalidArgumentException
     */
    public function setUrl($url)
    {
        //todo: проверить валидность URL
        $this->url = $url;
        $data = explode('/', $url);
        if (($data['0'] != 'http:') && ($data['0'] != 'https:')) {
            throw new InvalidArgumentException('Has given wrong URL');
        }
        $this->baseUrl = $data[0] . '//' . $data[2];
    }

    /**
     *  Getting content of web page
     * @return string <p>
     * Content of web page.
     * </p>
     * @throws RuntimeException
     */
    public function getPageContent(): string
    {
        if (!$this->url) {
            throw new RuntimeException('URL not defined. Please define the URL');
        }

        $curlObject = curl_init();
        curl_setopt($curlObject, CURLOPT_URL, $this->url);
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, 1);
        $this->pageContent = curl_exec($curlObject);
        $error = curl_errno($curlObject);
        curl_close($curlObject);

        if ($error) {
            throw new RuntimeException('Page ' . $this->url . ' not can\'t be opened');
        }
        return $this->pageContent;
    }


    /**
     * Getting links from <img> tags from web page
     * @return array <p>
     * Array of prepared for downloading links.
     * </p>
     * @throws ImgLoaderException
     */
    public function getImageLinks(): ?array
    {
        $imgArray = [];
        if (!preg_match_all('/<img.+src="(.+?)".+?>/imx', $this->pageContent, $imgArray)) {
            throw new ImgLoaderException('The page does not contain the images');
        }

        $this->prepareImageUrl($imgArray[1]);
        return $this->imageLinksArray;
    }

    /**
     * Prepare the images links for getting images files
     * @param array $urlPicArray <p>
     * Array of raw urls from <img> tags.
     * </p>
     * @throws ImgLoaderException
     */
    private function prepareImageUrl(array $urlPicArray)
    {
        $resultArray = [];

        foreach ($urlPicArray as $url) {
            $explodedUrl = explode('.', $url);
            $fileType = $explodedUrl[count($explodedUrl) - 1];


            if (!in_array($fileType, self::IMG_TYPES, true)) {
                continue;
            }
            $urlSegments = explode('/', $url);
            $fileName = $urlSegments[count($urlSegments) - 1];

            if (($explodedUrl['0'] == 'http:') || ($explodedUrl['0'] == 'https:')) {
                $fullLink = $url;
            } else {
                $fullLink = $this->baseUrl . $url;
            }

            $resultArray[] = array(
                'url' => $fullLink,
                'file' => $fileName,
            );

        }
        if (!$resultArray) {
            throw new ImgLoaderException('Page does not contain the direct links to Images');
        }
        $this->imageLinksArray = $resultArray;
    }


    /**
     * Download images from web page
     * @return int <p>
     * Number of downloaded files.
     * </p>
     * @throws ImgLoaderException
     */
    public function downloadImages(): int
    {
        $this->getPageContent();
        $this->getImageLinks();

        $fileCounter = 0;

        if (!is_array($this->imageLinksArray)) {
            throw new RuntimeException('Links for downloading are missing');
        }

        $curlObject = curl_init();
        curl_setopt($curlObject, CURLOPT_HEADER, 0);
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObject, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curlObject, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11");


        foreach ($this->imageLinksArray as $link) {
            $imgContent = null;

            curl_setopt($curlObject, CURLOPT_URL, $link['url']);
            $imgContent = curl_exec($curlObject);

            $path = $this->picFolder . '/' . time() . '_' . $link['file'];

            if (!$imgContent) {
                continue;
            }

            if (file_put_contents($path, $imgContent)) {
                $fileCounter++;
            }
        }
        curl_close($curlObject);

        if (!$fileCounter) {
            throw new ImgLoaderException('Found ' . count($this->imageLinksArray) . ' links to images, but 0 was downloaded due to wrong links');
        }

        return $fileCounter;
    }
}