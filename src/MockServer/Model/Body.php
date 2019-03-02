<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 3/2/2019
 * Time: 10:19 PM
 */

namespace MockServer\Model;


abstract class Body extends Not
{

    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public abstract function getValue();

    //@JsonIgnore
    public function getRawBytes()
    {
        return $this->toString()->getBytes(UTF_8);
    }

    //@JsonIgnore
    public function getCharset(Charset $defaultIfNotSet)
    {
        if ($this instanceof BodyWithContentType) {
            return $this->getCharset($defaultIfNotSet);
        }
        return $defaultIfNotSet;
    }

    public function getContentType()
    {
        if ($this instanceof BodyWithContentType) {
            return $this->getContentType();
        }
        return null;
    }
}