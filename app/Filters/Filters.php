<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 22/07/2015
 * Time: 16:41
 */

namespace Madokami\Filters;


class Filters implements \ArrayAccess, \Iterator {

    protected $filters;

    public function __construct($filters) {
        if(is_array($filters)) {
            $this->filters = $filters;
        }
        else {
            $this->filters = array();
        }
    }

    public function __set($field, $value) {
        $this->filters[$field] = $value;
    }

    public function __get($field) {
        if(array_key_exists($field, $this->filters)) {
            return $this->filters[$field];
        }
        else {
            return null;
        }
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->filters[] = $value;
        } else {
            $this->filters[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->filters[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->filters[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->filters[$offset]) ? $this->filters[$offset] : null;
    }

    public function current() {
        return current($this->filters);
    }

    public function key() {
        return key($this->filters);
    }

    public function next() {
        return next($this->filters);
    }

    public function rewind() {
        return reset($this->filters);
    }

    public function valid() {
        return $this->offsetExists(key($this->filters));
    }

    public function has($key) {
        return $this->offsetExists($key);
    }

    public function get($key) {
        return $this->offsetGet($key);
    }

    public function queryParameters() {
        return [ 'filters' => $this->filters ];
    }

    public function url($modify = [ ], $remove = [ ]) {
        $params = array_merge($this->filters, $modify);
        $params = array_diff_key($params, array_flip($remove));
        return \URL::current().'?'.http_build_query([ 'filters' => $params ]);
    }

}