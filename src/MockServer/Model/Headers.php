<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 3/2/2019
 * Time: 10:23 PM
 */

namespace MockServer\Model;


class Headers extends KeysToMultiValues {

    public function __construct($headers) {
        $this->withEntries($headers);
    }

    public function build($name, $values) {
        return new Header($name, $values);
    }

    public function clone() {
        return (new Headers())->withEntries($this->getEntries());
    }
}