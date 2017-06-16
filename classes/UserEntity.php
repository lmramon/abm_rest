<?php

class UserEntity
{
    protected $id;
    protected $name;
    protected $email;
    protected $image;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data)
    {
        // no id if we're creating
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }

        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->image = $data['image'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getImage()
    {
        return $this->image;
    }
}
