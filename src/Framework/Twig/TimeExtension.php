<?php

namespace Framework\Twig;

class TimeExtension extends \Twig_Extension
{
    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(?\DateTime $date, string $format = 'd/m/Y H:i')
    {
        if (is_null($date)) {
            return 'WTFFFFF';
        }
        return '<span class="timeago" datetime="' .
            $date->format(\DateTime::ISO8601) .
            '">' . $date->format($format) . '</span>';
    }
}
