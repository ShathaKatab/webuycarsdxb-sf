<?php

namespace Wbc\UtilityBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class MarkdownTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twig.markdown_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class MarkdownTwigExtension extends \Twig_Extension
{
    /**
     * @var Markdown
     */
    private $parser;

    /**
     * MarkdownTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "parser" = @DI\Inject("wbc.utility.twig.markdown")
     * })
     *
     * @param Markdown $parser
     */
    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'md2html',
                [$this, 'markdownToHtml'],
                ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function markdownToHtml($content)
    {
        return $this->parser->toHtml($content);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'markdown_extension';
    }
}
