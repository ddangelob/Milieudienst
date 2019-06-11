<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class CommentFormModel
{

    private $title;

    private $message;


    public function __construct()
    {
        $this->title = $this->getTitle();
        $this->message = $this->getMessage();
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getMessage(): ?string
    {
        return $this->message;
    }
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

}