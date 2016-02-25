<?php
    namespace GSMap;
    abstract class BaseDataClass {
        final public function  __construct($data) {
            $this->set($data);
        }
        final public function toArray() {
            return get_object_vars($this);
        }
        final public function set(array $values) {
            foreach ($values as $k=>$v) {
                if (property_exists(get_class($this), $k) === true) {
                    $this->$k = $v;
                }
            }
        }
    }
?>