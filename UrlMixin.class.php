<?

class UrlMixin extends Mixin
{
  static $__prefix = 'url';
  
  static function sprintf()
  {
    $args = func_get_args();
    $template = array_shift($args);
    foreach($args as $k=>$v)
    {
      $args[$k] = u($v);
    }
    array_unshift($args, $template);
    $url = call_user_func_array('sprintf', $args);
    return $url;
  }

  static function fetch($url, $headers = array(), $http_user='', $http_pass='',$use_cache=null)
  {
    $config = W::module('url');
    if($use_cache===null) $use_cache = $config['use_cache'];
    $keys = array_merge(array($url, $http_user, $http_pass), array_values($headers));
    $md5 = md5(join('|',$keys));
    $fname = $config['cache_fpath']."$md5";
    if($use_cache && file_exists($fname)) return json_decode(file_get_contents($fname),true);
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Display communication with server
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return data instead of display to std out
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($http_user)
    {
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
      curl_setopt($ch, CURLOPT_USERPWD, "{$http_user}:{$http_pass}");
    }                     
    $data = curl_exec($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    $res = array($data, $error, $info);
    if($use_cache)
    {
      file_put_contents($fname,json_encode($res));    
    } 
    return $res;
  }     
}