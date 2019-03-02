<?php

namespace MockServer\Model;

class Parameters extends KeysToMultiValues {

    public function __construct($parameters = []) {
        $this->withEntries($parameters);
    }

    public function build(NottableString $name, $values) {
        return new Parameter($name, $values);
    }

    public function clone() {
        return (new Parameters())->withEntries($this->getEntries());
    }
}