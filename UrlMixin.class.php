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
}