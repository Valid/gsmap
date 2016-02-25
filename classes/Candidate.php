<?php
namespace GSMap;
class Candidate extends BaseDataClass {
    public $id ='';
    public $name = '';
    public $image = '';
    public $points = array();
    public $issues = array();
    public $district = null;
    public $state = null;
    public $seat = null;
}
?>