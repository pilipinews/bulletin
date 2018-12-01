<?php

namespace Pilipinews\Website\Bulletin;

use Pilipinews\Common\Article;
use Pilipinews\Common\Client;
use Pilipinews\Common\Crawler as DomCrawler;
use Pilipinews\Common\Interfaces\ScraperInterface;
use Pilipinews\Common\Scraper as AbstractScraper;

/**
 * Manila Bulletin Scraper
 *
 * @package Pilipinews
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class Scraper extends AbstractScraper implements ScraperInterface
{
    /**
     * @var string[]
     */
    protected $removables = array(
        '.uk-article-title',
        '.share-container',
        '.uk-grid.uk-grid-large.uk-margin-bottom',
        '.uk-visible-small.uk-margin-top.uk-margin-bottom',
        '#related_post',
        '#disqus_thread',
        'script',
    );

    /**
     * Returns the contents of an article.
     *
     * @param  string $link
     * @return \Pilipinews\Common\Article
     */
    public function scrape($link)
    {
        $this->prepare(mb_strtolower($link));

        $title = $this->title('.uk-article-title');

        $this->remove((array) $this->removables);

        $body = $this->body('article');

        $body = $this->image($body);

        $body = $this->slidenav($body);

        return new Article($title, $this->html($body));
    }

    /**
     * Converts image elements to readable string.
     *
     * @param  \Pilipinews\Common\Crawler $crawler
     * @return \Pilipinews\Common\Crawler
     */
    protected function image(DomCrawler $crawler)
    {
        $callback = function (DomCrawler $crawler) {
            $result = $crawler->filter('img')->first();

            $image = (string) $result->attr('src');

            $text = $crawler->filter('p')->first();

            $message = $image . ' - ' . $text->html();

            $message = str_replace('<br>', ' ', $message);

            return '<p>PHOTO: ' . $message . '</p>';
        };

        return $this->replace($crawler, '.wp-caption', $callback);
    }

    /**
     * Initializes the crawler instance.
     *
     * @param  string $link
     * @return void
     */
    protected function prepare($link)
    {
        $response = Client::request((string) $link);

        $regex = '/<p>Tags:(.*?)<\/p>/i';

        $html = preg_replace($regex, '', $response);

        $this->crawler = new DomCrawler((string) $html);
    }

    /**
     * Converts an slidenav element into a readable string.
     *
     * @param  \Pilipinews\Common\Crawler $crawler
     * @return \Pilipinews\Common\Crawler
     */
    protected function slidenav(DomCrawler $crawler)
    {
        $callback = function (DomCrawler $crawler) {
            $items = $crawler->filter('img');

            $items = $items->each(function ($crawler) {
                $link = 'https://news.mb.com.ph';

                $image = $link . $crawler->attr('src');

                return '<p>PHOTO: ' . $image . '</p>';
            });

            return implode("\n\n", (array) $items);
        };

        $class = '.uk-slidenav-position';

        return $this->replace($crawler, $class, $callback);
    }
}
