<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 3/2/2019
 * Time: 10:03 PM
 */

namespace MockServer\Model;


class Parameter extends KeyToMultiValue {

    public function __construct($name, String... $value) {
        parent::__construct($name, $value);
    }
}