<?php
class Model {
    public $relations = [];
    public function toArray() {
        $arr = [];
        foreach ($this->relations as $key => $value) {
            // Laravel's basic serialization uses camel to snake case unless configured otherwise
            $arr[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))] = $value;
        }
        return $arr;
    }
}
$m = new Model();
$m->relations['chucNangs'] = [1,2,3];
echo json_encode($m->toArray(), JSON_PRETTY_PRINT);
