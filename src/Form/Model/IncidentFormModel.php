<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class IncidentFormModel
{
    /**
     * @Assert\NotBlank(message="Choose a Category!")
     * @Assert\NotNull(message="Choose a Category!")
     */
    private $category;

    /**
     * @Assert\NotBlank(message="Choose a Status!")
     * @Assert\NotNull(message="Choose a Status!")
     */
    private $status;

    /**
     * @Assert\NotBlank(message="Fill in an priority!")
     * @Assert\NotNull(message="Fill in an priority!")
     * @Assert\Regex("/^[0-9].{0}$/", message="Select an priority between 0 and 9")
     */
    private $priority;

    /**
     * @Assert\NotBlank(message="Choose a Location!")
     * @Assert\NotNull(message="Choose a Location!")
     */
    private $location;

    /**
     * @Assert\Type("string", message="Please fill in text.")
     * @Assert\NotBlank(message="Choose a Title!")
     * @Assert\NotNull(message="Choose a Title!")
     * @Assert\Length(min=5, minMessage="Please fill in a longer title!")
     */
    private $title;

    /**
     * @Assert\Type("string", message="Please fill in text.")
     * @Assert\NotBlank(message="Choose a description!")
     * @Assert\NotNull(message="Choose a description!")
     * @Assert\Length(min=5, minMessage="Please fill in a longer description!")
     */
    private $description;

    public function __construct()
    {
        $this->category = $this->getCategory();
        $this->status = $this->getStatus();
        $this->location = $this->getLocation();
        $this->title = $this->getTitle();
        $this->description = $this->getDescription();
    }
    public function getCategory(): ?int
    {
        return $this->category;
    }
    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getLocation(): ?int
    {
        return $this->location;
    }
    public function setLocation(int $location): self
    {
        $this->location = $location;

        return $this;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getPriority(): ?int
    {
        return $this->priority;
    }
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

}