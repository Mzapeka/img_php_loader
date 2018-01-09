<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 09.01.18
 * Time: 18:02
 */

namespace Mzapeka\ImgLoader;

use PHPUnit\Framework\TestCase;


/**
 * Class ImgLoaderTest
 * @package Mzapeka\ImgLoader
 * @property ImgLoader $entity
 */
class ImgLoaderTest extends TestCase
{

    public $entity;

    public function setUp()
    {
        $this->entity = new ImgLoader();
    }

    public function tearDown()
    {
        $this->entity = null;
    }

    public function invalidPath()
    {
        return [
            'bool' => [true],
            'string' => ['aabcdefg'],
            'int' => [123],
            'wrongPath' => [__DIR__ . '/testfolder']
        ];
    }

    public function validPath()
    {
        return [
            'validPath' => [__DIR__ . '/testpath']
        ];
    }

    /**
     * @dataProvider invalidPath
     */
    public function testSetPicFolderRaisesRuntimeExeption($path)
    {
        $this->expectException('\InvalidArgumentException');
        $this->entity->setPicFolder($path);
    }

    /**
     * @dataProvider validPath
     */
    public function testSetPicFolderIsValid($path)
    {

        $this->entity->setPicFolder($path);
        $this->assertAttributeEquals($path, 'picFolder', $this->entity, $path);
    }

    public function invalidUrl()
    {
        return [
            'bool' => [true],
            'string' => ['aabcdefg'],
            'int' => [123],
            'wrongUrl' => ['www.test.com'],
            'wrongUrl2' => ['test.com'],
            'wrongUrl3' => ['http//test.com'],
        ];
    }

    /**
     * @dataProvider invalidUrl
     */
    public function testSetUrlRisesRuntimeException($url)
    {
        $this->expectException('\InvalidArgumentException');
        $this->entity->setUrl($url);
    }


    public function testSetUrlIsValidHttp()
    {
        $url = 'http://test.com/test/test.php';
        $this->entity->setUrl($url);
        $this->assertAttributeEquals($url, 'url', $this->entity, $url);
        $this->assertAttributeEquals('http://test.com', 'baseUrl', $this->entity);
    }

    public function testSetUrlIsValidHttps()
    {
        $url = 'https://test.com/test2/test2.php';
        $this->entity->setUrl($url);
        $this->assertAttributeEquals($url, 'url', $this->entity, $url);
        $this->assertAttributeEquals('https://test.com', 'baseUrl', $this->entity);
    }

    public function invalidUrlForGetPage()
    {
        return array(
            'url_not_defined' => [''],
            'invalid_URL' => ['https://gith']
        );
    }

    /**
     * @dataProvider invalidUrlForGetPage
     */
    public function testGetPageContentRisesRuntimeException($url)
    {
        $this->entity->url = $url;
        $this->expectException('\RuntimeException');
        $this->entity->getPageContent();
    }

    public function testGetPageContentIsValid()
    {
        $this->entity->url = 'https://github.com';
        $this->entity->getPageContent();
        $this->assertAttributeNotEmpty('pageContent', $this->entity, $this->entity->pageContent);
    }

    public function invalidContentForGetImageLinks()
    {
        return array(
            'page w/o <img> tags' => [file_get_contents(__DIR__ . '/testpath/testEmpty.html')],
            'page w/o valid images tags' => [file_get_contents(__DIR__ . '/testpath/testInvalidImage.html')]
        );
    }

    /**
     * @dataProvider invalidContentForGetImageLinks
     */

    public function testGetImageLinksRisesImgLoaderException($content)
    {
        $this->expectException('\Mzapeka\ImgLoader\Exceptions\ImgLoaderException');
        $this->entity->setUrl('http://test.com/test');
        $this->entity->pageContent = $content;
        $this->entity->getImageLinks();
    }

    public function testGetImageLinksIsValid()
    {
        $this->entity->setUrl('http://test.com/test');
        $this->entity->pageContent = file_get_contents(__DIR__ . '/testpath/testValid.html');
        $this->entity->getImageLinks();
        $this->assertCount(3, $this->entity->imageLinksArray);
        $this->assertEquals('http://test.com/chivas.jpg', $this->entity->imageLinksArray[0]['url']);
    }

}