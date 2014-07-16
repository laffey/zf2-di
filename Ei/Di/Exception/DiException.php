<?php
/**
 * di exception
 * 
 */

namespace Ei\Di\Exception;

class DiException extends \Exception
{
    
    /* >= 10000 severe issue */
    const DI_CONFIG_NOT_LOADED             = 11000;
    const DI_CONFIG_KEY_NOT_FOUND          = 11100;
    const DI_CONFIG_INVALID_FORMAT         = 11110;
    const DI_APP_CONFIG_NOT_LOADED         = 11200;
    const DI_APP_CONFIG_KEY_MISSING        = 11210;
    const CIRCULAR_DEPENDENCY              = 11300;
    
    /* < 10000 important, but common exception */
    
    /* < 1000 minor exception */
    
}
