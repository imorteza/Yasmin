<?php
/**
 * Yasmin
 * Copyright 2017 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: MIT
*/

namespace CharlotteDunois\Yasmin\Structures;

/**
 * Something all structures extend. Do not use this.
 * @access private
 */
class Structure implements \JsonSerializable, \Serializable { //TODO: Nya
    protected $client;
    static public $serializeClient;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client) {
        $this->client = $client;
    }
    
    function __get($name) {
        switch($name) {
            case 'client':
                return $this->client;
            break;
        }
        
        return null;
    }
    
    function __debugInfo() {
        $vars = get_object_vars($this);
        unset($vars['client']);
        return $vars;
    }
    
    function jsonSerialize() {
        return get_object_vars($this);
    }
    
    function serialize() {
        return serialize(get_object_vars($this));
    }
    
    function unserialize($data) {
        $exp = \ReflectionMethod::export($this, '__construct', true);
        preg_match('/Parameters \[(\d+)\]/', $exp, $count);
        $count = $count[1];
        
        switch($count) {
            default:
                throw new \Exception('Can not unserialize a class with more than 2 arguments');
            break;
            case 1:
                $this->__construct(unserialize($data));
            break;
            case 2:
                $this->__construct(\CharlotteDunois\Yasmin\Structures\Structure::$serializeClient, unserialize($data));
            break;
        }
    }
    
    function _patch(array $data) {
        foreach($data as $key => $val) {
            if(\property_exists($this, $key)) {
                if($this->$key instanceof \CharlotteDunois\Yasmin\Structures\Collection) {
                    if(!\is_array($val)) {
                        $val = array($val);
                    }
                    
                    foreach($val as $element) {
                        $instance = $this->$key->get($element['id']);
                        $instance->_patch($element);
                    }
                } else {
                    $this->$key = $val;
                }
            }
        }
    }
}
