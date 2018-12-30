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
        }
        trim($attributes['class']);

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif (array_key_exists('options', $opts)) {
            $input = $this->select($value, $opts['options'], $attributes);
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
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input " . $this->getHTMLFromArray($attributes) . " type=\"text\" value=\"{$value}\">";
    }

    /**
     * Generates a textarea
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHTMLFromArray($attributes) . ">{$value}</textarea>";
    }

    /**
     * @param string|null $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHTMLFromArray($params) . '>' . $options[$key] . '</option>';
        }, '');
        return "<select " . $this->getHTMLFromArray($attributes) . ">{$htmlOptions}</select>";
    }

    /**
     * Generates a string from an array of HTML attributes
     * @param array $attributes
     * @return string
     */
    private function getHTMLFromArray(array $attributes): string
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string)$key;
            } elseif ($value != false) {
                $htmlParts[] = $key . '="' . $value . '"';
            }
        }
        return implode(' ', $htmlParts);
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
