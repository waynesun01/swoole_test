<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of autoload
 *
 * @author WayneSun
 */
class autoload {
     public static function load($class){
        require BASEDIR . '/'.str_replace('\\', '/', $class).'.php';
    }
}
spl_autoload_register('autoload::load');