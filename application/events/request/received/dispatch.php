<?php

class events_request_received_dispatch
{
    const CONTROLLER_PREFIX  = "controller_";
    const DEFAULT_CONTROLLER = "index";
    const DEFAULT_METHOD     = "index";

    static public function handler($data = null)
    {
        self::dispatch(self::getRequestFromUri());
    }

    static private function getRequestFromUri()
    {
        // crop out the get request of the uri
        list($uri) = explode("?", $_SERVER['REQUEST_URI']);
        Log::$uri = $uri;
        
        // filter out all blank segments of the uri
        $request = array_values(array_filter(explode('/', $uri), function($n)
        {
            return ($n !== null && $n !== '');
        }));

        // if the uri is empty default it to index
        if (!count($request)) {
            $request = array(self::DEFAULT_CONTROLLER, self::DEFAULT_METHOD);
        }
        return $request;
    }

    static private function dispatch($request)
    {	
        $controller = self::CONTROLLER_PREFIX . array_shift($request);
		
        // if no route is set then default to index
        if (!count($request))
        {
            $request = array(0 => self::DEFAULT_METHOD);
        }
		
        if (!class_exists($controller))
            $controller = "Controller";
        
		$class = new $controller($request);
        echo $class;
    }
    
}
