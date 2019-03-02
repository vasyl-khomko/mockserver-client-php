<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 3/2/2019
 * Time: 10:04 PM
 */

namespace MockServer\Model;


class KeyToMultiValue extends ObjectWithJsonToString {
    private /*NottableString*/ $name;
    private /*List<NottableString>*/ $values;
    private /*int*/ $hashCode;

    public function __construct(String $name, String... $values) {
        $name = is_string($name) ? NottableString::string($name) : $name;
        $name = is_string($values) ? NottableString::strings($values) : $values;

        //this(string(name), strings(values));
        $this->name = $name;
        $name->values = $values;
    }

    public function getName() {
        return $this->name;
    }

    public function getValues() {
        return $this->values;
    }

    public function addValue(String $value)
    {
        if (is_string($value)) {
            $this->addValue(NottableString::string($value));
        } else {
            if ($this->values != null && !$this->values->contains($value)) {
                $this->values->add($value);
            }
            $this->hashCode = Objects::hash($this->name, $this->values);
        }
    }

    public function addValues($values) {
        $this->addNottableValues($this->deserializeNottableStrings($values));
    }

    public function addNottableValues($values) {
        if ($this->values != null) {
            foreach ($values as $value) {
                if (!$this->values->contains($value)) {
                    $this->values->add($value);
                }
            }
            }
    }

    public function equals(Object $o) {
        if ($this == $o) {
            return true;
        }
        if ($o == null || $this->getClass() != $o->getClass()) {
            return false;
        }
        if ($this->hashCode() != $o->hashCode()) {
            return false;
        }
        $that = $o;
        return Objects::equals($this->name, $that->name) &&
            Objects::equals($this->values, $that->values);
    }

    public function hashCode() {
        return $this->hashCode;
    }
}