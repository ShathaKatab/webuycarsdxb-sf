<?php

namespace Wbc\UtilityBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class Markdown.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twig.markdown")
 */
class Markdown
{
    /**
     * @var \Parsedown
     */
    private $parser;

    /**
     * Markdown Constructor.
     */
    public function __construct()
    {
        $this->parser = new \Parsedown();
    }

    /**
     * Converts markdown to HTML.
     *
     * @param $text
     *
     * @return string
     */
    public function toHtml($text)
    {
        $html = $this->parser->text($text);

        return $html;
    }
}
