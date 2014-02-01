<?php
/**
 * @author Alex Raven
 * @company ESITEQ
 * @website http://www.esiteq.com/
 * @email bugrov at gmail.com
 * @created 29.10.2013
 * @version 1.0
 * improved by Rolland (rolland at alterego.biz.ua)
 */

if(!defined('DIRECTORY_SEPARATOR')){
    define('DIRECTORY_SEPARATOR','/'); 
}

class Bender
{
    // CSS minifier
    public $cssmin = "cssmin";
    // JS minifier, can be "packer" or "jshrink"
    public $jsmin = "packer";
    // Project's root dir
    private $root_dir;
    // version key for src modifying
    public $version_key = 'v';
    // developer mode
    public $dev_mode = false;
    // arrays for files
    private $javascripts = array();
    private $stylesheets = array();
    
    // Constructor
    public function __construct($config=array()){
        $this->root_dir = defined( 'ROOT_DIR' ) ? ROOT_DIR : $_SERVER['DOCUMENT_ROOT'];
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }
    // Enqueue CSS or Javascript
    public function enqueue( $src ){
        if ( !is_array( $src ) ){
            $src = array( $src );
        }
        foreach ( $src as $s ){
            switch (self::get_ext( $s )){
                case "css":
                    $this->stylesheets[] = $s;
                    break;
                case "js":
                    $this->javascripts[] = $s;
                    break;
            }
        }
    }  
    
    // Print output for Javascript
    public function output_js($output_dir,$output_prefix){
        if(!$this->dev_mode){
            $output = $this->minify_js( $this->javascripts, $output_dir,$output_prefix );
            $this->javascripts = array();
            return $this->make_script($output);    
        }else{
            $ret = '';
            foreach($this->javascripts as $file){
                $ret .= $this->make_script($file);
            }
            $this->javascripts = array();
            return $ret;
        }
    }
    // Print output for CSS
    public function output_css($output_dir,$output_prefix ){
        if(!$this->dev_mode){
            $output = $this->minify_css( $this->stylesheets, $output_dir,$output_prefix );
            $this->stylesheets = array();
            return $this->make_link($output);    
        }else{
            $ret = '';
            foreach($this->stylesheets as $file){
                $ret .= $this->make_link($file);
            }
            $this->stylesheets = array();
            return $ret;
        }
        
    }
    
    // Minify Javascripts and write output
    protected function minify_js( $files, $output_dir,$output_prefix ){
        $hash = $this->get_hash($files);
        $output = $output_dir.'/'.$output_prefix.'_'.$hash.'.js';  
        if (file_exists($this->root_dir.'/'.$output) && is_file($this->root_dir.'/'.$output)){
            return $output;
        }
        $str = $this->join_files($files);
        switch ( $this->jsmin ){
            case "packer":
                require_once realpath( __DIR__ ) . "/class.JavaScriptPacker.php";
                $packer = new JavaScriptPacker( $str, "Normal", true, false );
                $packed = $packer->pack();
                break;
            case "jshrink":
                require_once realpath( __DIR__ ) . "/JShrink.class.php";
                $packed = JShrink\Minifier::minify( $str );
                break;
            default:
                $packed = $str;
        }
        file_put_contents($this->root_dir.'/'.$output, $packed );
        $this->remove_old($output_dir,$output_prefix,$hash,'js');
        return $output;
    }
    
    // Minify CSS and write output
    protected function minify_css( $files, $output_dir,$output_prefix ){
        $hash = $this->get_hash($files);
        $output = $output_dir.'/'.$output_prefix.'_'.$hash.'.css';  
        if (file_exists($this->root_dir.'/'.$output) && is_file($this->root_dir.'/'.$output)){
            return $output;
        }
        $str = $this->join_files_css($files,$output_dir);      
        switch ( $this->cssmin ){
            case "cssmin":
                require_once realpath( dirname( __file__ ) . "/cssmin.php" );
                $packed = CssMin::minify( $str );
                break;
            default:
                $packed = $str;
        }  
        file_put_contents( $output, $packed );
        $this->remove_old($output_dir,$output_prefix,$hash,'css');
        return $output;
    }
    
    // Join array of files into a string
    protected function join_files( $files ){
        if ( !is_array( $files ) ){
            return "";
        }
        $c = "";
        foreach ( $files as $file ){
            $c .= file_get_contents($this->root_dir.'/'.$file);
        }
        return $c;
    }
    
    // Join array of css files into a string after preparing
    protected function join_files_css( $files, $output_dir )
    {
        if ( !is_array( $files ) ){
            return "";
        }
        $c = "";
        foreach ( $files as $file ){
            $c .= $this->prepared_css($this->root_dir.'/'.$file, $this->root_dir.'/'.$output_dir);
        }
        return $c;
    }
    
    // Get extension in lowercase
    protected static function get_ext( $src ){
        return strtolower( pathinfo( $src, PATHINFO_EXTENSION ) );
    }
    
    /**
    * returns src for resource due to filemtime
    */
    protected function get_src($output){
        return '/'.$output.'?'.$this->version_key.'='.filemtime($this->root_dir.'/'.$output);    
    }
    
    protected function make_link($output){
        return '<link href="' . $this->get_src($output) . '" rel="stylesheet" type="text/css"/>';
    }
    
    protected function make_script($output){
        return '<script type="text/javascript" src="' . $this->get_src($output) . '"></script>';
    }
    
    /**
    * removes old file for given cache prefix
    */
    private function remove_old($output_dir,$output_prefix,$hash,$ext){
        $all_siblings = scandir($this->root_dir.'/'.$output_dir);
        foreach($all_siblings as $name){
            if(
                !preg_match('#'.$output_prefix.'_[0-9a-z]{32}.'.$ext.'#',$name)
                ||
                $name == $output_prefix.'_'.$hash.'.'.$ext
            ){
                continue;
            }
            unlink($this->root_dir.'/'.$output_dir.'/'.$name);
        }   
    }
    
    /**
    * generates a hash for given files due names and filemtime
    */
    private function get_hash($files){
        if ( !is_array( $files ) ){
            return false;
        }
        $str = "";
        foreach ( $files as $file ){
            $str .= $file.''.filemtime($this->root_dir.'/'.$file);
        }
        return md5($str);    
    }
    
    /**
    * handles all @import and url() in css file and returns result
    */
    private function prepared_css($file, $output_dir){
        $content = file_get_contents($file);
        $source_dir = realpath(dirname($file));  
        $output_dir = realpath($output_dir);
        // if file has imports
        $import_replacements = array();
        if(strpos($content,'@import') !== FALSE){  // quick check
            // find all @import strings and replace them to imported content
            if(preg_match_all('#@import (?:url\()?["\']?([^")]+)["\']?[)]?;#',$content,$matches)){
                foreach($matches[0] as $index=>$match){
                    if(strpos($matches[1][$index],'/') === 0){
                        // absolute url 
                        $imported = $this->prepared_css($this->root_dir.'/'.$matches[1][$index],$output_dir);
                    }else{
                        // relative_url
                        $imported = $this->prepared_css($source_dir.'/'.$matches[1][$index],$output_dir);
                    }
                    $placeholder = '||'.$matches[1][$index].'||' ;
                    $import_replacements[$placeholder] = $imported;
                    // replace 
                    $content = str_replace($match,$placeholder,$content);
                }
            }               
        }
        // if directories are different - we need to change relative urls
        if($source_dir != $output_dir){
            $source_levels = explode(DIRECTORY_SEPARATOR,trim($source_dir,DIRECTORY_SEPARATOR));
            $output_levels = explode(DIRECTORY_SEPARATOR,trim($output_dir,DIRECTORY_SEPARATOR));
            // перебирать массивы путей от корня, пока не наткнемся на различия. 
            for($i=0;;$i++){
                if(empty($source_levels[$i]) || empty($output_levels[$i])){
                    break;
                }
                if($source_levels[$i] == $output_levels[$i]){
                    unset($source_levels[$i]);
                    unset($output_levels[$i]);
                }
            }
            $source_levels = array_values($source_levels);
            $output_levels = array_values($output_levels);
            $diff_arr = array_merge(
                sizeof($output_levels)?array_fill(0,sizeof($output_levels),'..'):array(),
                $source_levels
            );
            // if we need change relative urls
            if(strpos($content,'url(') !== FALSE){ // quick check
                $replaced = array();
                if(preg_match_all('#url\(["\']?([^)"\']+)["\']?\)#',$content,$matches)){
                    foreach($matches[1] as $url){
                        if(isset($replaced[$url])){
                            continue;
                        }
                        $_diff_arr = $diff_arr;
                        $_match_parts = explode('/',$url);
                        $_add = false;
                        do{
                            $_s = array_shift($_match_parts);    
                            $_o = array_pop($_diff_arr);    
                            if(empty($_o) || empty($_s)){
                                break;
                            }
                            if($_o=='..' || $_s!='..'){
                                $_add = true;
                                break;
                            }
                        }while(1);        
                        $_diff = join('/',$_diff_arr)
                            .($_add?'/'.$_o:'');
                        if(!empty($_diff)){
                            $_diff .='/';
                        }
                        $_diff .= 
                            (($_s!='..' || empty($_o))?$_s.'/':'')
                            .join('/',$_match_parts);
                        $_diff = rtrim($_diff,'/');
                        $content = str_replace($url,$_diff,$content);
                        $replaced[$url] = true;
                    }
                }
            }
        }
        // if we have some imported to replace
        foreach($import_replacements as $placeholder=>$imported){
            $content = str_replace($placeholder,$imported,$content);
        }
        return $content;  
    }
    
}