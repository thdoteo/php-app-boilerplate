<?php

namespace Framework\Twig;

class FormExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     *  Generates a field
     * @param array $context
     * @param string $key
     * @param mixed|null $value
     * @param string|null $label
     * @param array $opts
     * @return string
     */
    public function field(array $context, string $key, $value = null, ?string $label = null, array $opts = []): string
    {
        $type = $opts['type'] ?? 'text';
        $value = $this->convertValue($value);
        $error = $this->getErrorHTML($context, $key);
        $attributes = [
            'class' => 'form-control',
            'name' => $key,
            'id' => $key
        ];
        if (isset($opts['class'])) {
            $attributes['class'] .= ' ' . $opts['class'];
        }
        if ($error) {
            $attributes['class'] .= ' is-invalid';
        } else {
//            $attributes['class'] .= ' is-valid';
        }
        trim($attributes['class']);

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }

        return "<div class=\"form-group\">
            <label for=\"{$key}\">{$label}</label>
            {$input}
            {$error}
        </div>";
    }

    /**
     * Generates the feedback text for the input's error
     * @param array $context
     * @param string $key
     * @return string
     */
    private function getErrorHTML(array $context, string $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<div class=\"form-text invalid-feedback\">{$error}</div>";
        }
        return "";
    }

    /**
     * Generates an input of type text
     * @param string|null $value
     * @param array $attrributes
     * @return string
     */
    private function input(?string $value, array $attrributes): string
    {
        return "<input " . $this->getHTMLFromArray($attrributes) . " type=\"text\" value=\"{$value}\">";
    }

    /**
     * Generates a textarea
     * @param string|null $value
     * @param array $attrributes
     * @return string
     */
    private function textarea(?string $value, array $attrributes): string
    {
        return "<textarea " . $this->getHTMLFromArray($attrributes) . ">{$value}</textarea>";
    }

    /**
     * Generates a string from an array of HTML attributes
     * @param array $attributes
     * @return string
     */
    private function getHTMLFromArray(array $attributes): string
    {
        return implode(' ', array_map(function ($key, $value) {
            return "{$key}=\"{$value}\"";
        }, array_keys($attributes), $attributes));
    }

    /**
     * Makes sure that value is a string
     * @param $value
     * @return string
     */
    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }
}
