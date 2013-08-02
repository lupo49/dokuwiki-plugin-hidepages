<?php
/**
 * DokuWiki Plugin hidepages (Syntax Component)
 * 
 * Syntax: ~~NOSIDEBAR~~ 
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Matthias Schulte <dokuwiki@lupo49.de>
 * @version    2013-08-02
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_hidepages extends DokuWiki_Syntax_Plugin {

    function getType(){ return 'substition'; }
    function getPType(){ return 'normal'; }
    function getSort(){ return 990; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~HIDEPAGE.*~~', $mode, 'plugin_hidepages');
    }
 
    function handle($match, $state, $pos, &$handler){
        $data = array();
        $match = hsc(trim($match));

        if($match == '~~HIDEPAGE~~') {
            if($this->getConf('hidefromsearch')) array_push($data, 'search');
            if($this->getConf('hidefromsitemap')) array_push($data, 'sitemap');
        } else {
            // extract parameters search and sitemap if passed
            $param = utf8_substr($match, 11, -2);

            // $param could be "sitemap" or "sitemap;search"
            if(utf8_strpos($param, ';') !== false) {
                $param = explode(';', $param);
            }

            if($param == 'sitemap' || (is_array($param) && in_array('sitemap', $param))) {
                array_push($data, 'sitemap');
            }

            if($param == 'search' || (is_array($param) && in_array('search', $param))) {
                array_push($data, 'search');
            }
        }

        return $data;
    }

    function render($mode, &$renderer, $data) {
        if($mode == "metadata") {
            // set flag in metadata to hide page in action component
            if(!is_array($data)) {
                $renderer->meta['hidepage']['sitemap'] = true;
                $renderer->meta['hidepage']['search'] = true;
            } else {
                if(in_array('sitemap', $data)) $renderer->meta['hidepage']['sitemap'] = true;
                if(in_array('search', $data))  $renderer->meta['hidepage']['search'] = true;
            }
        }
        return true;
    } 
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
