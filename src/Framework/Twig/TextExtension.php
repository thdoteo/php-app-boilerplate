<?php

namespace Framework\Twig;

/**
 * Collection of Twig extensions concerning text manipulation
 * @package Framework\Twig
 */
class TextExtension extends \Twig_Extension
{
    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * Returns an excerpt of a text
     * @param null|string $content
     * @param int $length
     * @return string
     */
    public function excerpt(?string $content, $length = 100): string
    {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $length) {
            $excerpt = mb_substr($content, 0, $length);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }

        return $content;
    }
}
