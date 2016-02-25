<?php
    namespace GSMap;
    class Data extends \ArrayObject {
        protected $path = '';
        protected $class = '';
        public function __construct($path, $class) {
            $this->path = $path;
            $this->class = $class;
            $this->read();
        }
        public function read() {
            $data = json_decode(file_get_contents($this->path), true);
            $className = $this->class;
            foreach ($data as $item) {
                $obj = new $className($item);
                $this->append($obj);
            }
        }
        public function write() {
            $data = $this->getArrayCopy();
            file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));
        }
        public function row($id, array $values=null) {
            if ($values == null) {
                $results = $this->getArrayCopy();
                $results = array_filter($results, function($elem) use($id){
                    return $elem->id === $id;
                });
                if (count($results) < 1) {
                    return null;
                }
                $className = $this->class;
                return array_shift($results);
            }
            $className = $this->class;
            $obj = new $className($values);
            $this->append($obj);
            return $obj;
        }

        public function where(array $conditions) {
            $results = $this->getArrayCopy();
            //print '<pre>';
            //var_dump($conditions);
            foreach ($conditions as $condition) {
                $results = array_filter($results, function ($elem) use ($condition, &$results) {
                    $prop = $condition[0];
                    //var_dump('cheching prop:'.$prop);
                    return $elem->$prop == $condition[1];
                });
            }
            return $results;
        }



        /**
         * Overridden method to return object
         *
         * @see ArrayObject::offsetGet()
         * @return void
         */
        public function offsetGet($offset) {
            $values = parent::offsetGet($offset);
            $className = $this->class;

            print 'offsetget: '.$index;

            return new $className($values);
        }

    }
?>