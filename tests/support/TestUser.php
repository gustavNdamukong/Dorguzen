<?php

namespace Dorguzen\Tests\Support;

/**
 * TestUser is used to simulate an object (in this case a user object)
 * filled with data can be passed around in the system's request
 * lifecycle successfully.
 */
#[\AllowDynamicProperties]
class TestUser
{
    public function make(array $attributes = []): self
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        // Ensure minimum identity
        $this->id = 1;

        return $this;
    }

    public function __set($member, $value)
    {
        $this->{$member} = $value;
    }


    /**
     * This member being retrieved must have been created already using __set() above
     */
    public function __get($member)
    {
        return $this->{$member};
    }
}
