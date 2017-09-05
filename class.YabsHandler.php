<?php 
class YabsHandler
{ 
    private static $posts = array();

    static public function get_posts($username, $tags = null, $page = 0, $limit = 100)
    {
        $key = $username.'-'.$tags.'-'.$page.'-'.$limit;

        if(isset(self::$posts[$key]))
            return self::$posts[$key];

        $p = incached::load($key);
        if($p['cache'])
			self::$posts[$key] = $p['cache'];
        
        if(empty(self::$posts[$key]))
        {
            $request = "https://www.yabs.io/a/view.php?user=$username&tags=$tags&page=$page&limit=$limit&r=json";
            self::$posts[$key] = @file_get_contents($request);
            if(!self::$posts[$key])
				self::$posts[$key] = $p['org'];
            else
            {
				self::$posts[$key] = json_decode(self::$posts[$key], true);
				incached::save($key, self::$posts[$key]);
			}
        }
        
        return (array)self::$posts[$key];
    }
}


class incached
{
   static private function name($uid)
   {
   		return 'incached_'.$uid;
   }
   
   static function load($uid, $minutes = 60)
   {
        $rv = array('cache'=>null, 'org'=>null);
        $filename = self::name($uid);
        if(!file_exists($filename))
            return $rv;
        
        //echo time().'-'.filemtime($filename).'='.time() - filemtime($filename);
        $rv['org'] = unserialize(file_get_contents($filename));
        if(time() - filemtime($filename) < ($minutes * 60))
            $rv['cache'] = $rv['org'];
        
        return $rv;        
   }
   
   static function save($uid, $data)
   {
        $filename = self::name($uid);
        file_put_contents($filename, serialize($data));
   }
   
   static function load_direct($uid) 
   {
        $filename = self::name($uid);
        if(!file_exists($filename))
            return null;
        
        return unserialize(file_get_contents($filename));
   }
}
