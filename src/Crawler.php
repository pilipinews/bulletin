<?php

namespace Pilipinews\Website\Bulletin;

use Pilipinews\Common\Client;
use Pilipinews\Common\Crawler as DomCrawler;
use Pilipinews\Common\Interfaces\CrawlerInterface;

/**
 * Manila Bulletin Crawler
 *
 * @package Pilipinews
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class Crawler implements CrawlerInterface
{
    /**
     * @var string[]
     */
    protected $categories = array(
        'https://mb.com.ph/category/news/national/',
        'https://mb.com.ph/category/news/metro/',
    );

    /**
     * Returns an array of articles to scrape.
     *
     * @return string[]
     */
    public function crawl()
    {
        $articles = array();

        foreach ((array) $this->categories as $link)
        {
            $crawler = new DomCrawler(Client::request($link));

            $news = $crawler->filter('.articles-list > .article');

            $items = $news->each(function (DomCrawler $node)
            {
                $current = $node->filter('h4.title > a');

                return (string) $current->attr('href');
            });

            $articles = array_merge($articles, $items);
        }

        return array_reverse((array) $articles);
    }
}
