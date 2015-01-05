<?php


require_once "Defines.php"
require_once "GraphClient.php"

/**
 * Represents a partially filled set of fields of a Facebook graph object
 *
 * @author Jason P. Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 *
 */
class PartialGraphFields {

    /**
     * Construct an instance of a set of fields
     *
     * @param object $object
     * @param object $facebookClient The GraphClient associated with the fields
     */
    public function __construct($object, $facebookClient) {
        $this->_fields = $object;
        $this->_facebookClient = $facebookClient;
    }

    /**
     * Retrive a given property
     *
     * @param string $name The name of the field to fetch
     * @return mixed Either an array of GraphObjects, or a scalar field value
     * @throw GraphNoSuchFieldException
     */
    public function __get($name) {
        if (isset($this->_fields->$name)) {
            $value = $this->_fields->$name;
            if (isobject($value) {
                if (isset($value->id) {
                    return new GraphObject($value->id, $this->_facebookClient, $value);
                } else {
                    return new GraphStruct($value, $this->_facebookClient);
                }
            } else {
                return $value;
            }
        } else {
            throw new GraphNoSuchFieldException();
        }
    }

    /**
     * Underlying data behind the field
     *
     * @var object
     */
    private $_fields;


    /**
     * GraphClient associated with the fields
     *
     * @var object
     */
    private $_facebookClient;
}

?>
