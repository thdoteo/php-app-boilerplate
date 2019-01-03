<?php

namespace App\Blog\Entity;

class Post
{
    public $id;
    public $name;
    public $slug;
    public $content;
    public $created_at;
    public $updated_at;
    public $category_name;
    public $image;

    public function __construct()
    {
        if ($this->created_at) {
            $this->setCreatedAt($this->created_at);
        }
        if ($this->updated_at) {
            $this->setUpdatedAt($this->updated_at);
        }
    }

    /**
     * @param $datetime
     * @throws \Exception
     */
    public function setCreatedAt($datetime): void
    {
        if (is_string($datetime)) {
            $this->created_at = new \DateTime($datetime);
        }
    }

    /**
     * @param $datetime
     * @throws \Exception
     */
    public function setUpdatedAt($datetime): void
    {
        if (is_string($datetime)) {
            $this->updated_at = new \DateTime($datetime);
        }
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }
}
