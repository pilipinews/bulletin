<?php

namespace Pilipinews\Website\Bulletin;

/**
 * Crawler Test
 *
 * @package Pilipinews
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class CrawlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CrawlerInterface::crawl.
     *
     * @return void
     */
    public function testCrawlMethod()
    {
        $crawler = new Crawler;

        $items = $crawler->crawl();

        $this->assertTrue(true);
    }
}
