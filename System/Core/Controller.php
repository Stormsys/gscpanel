<?php
using('View', 'Core');

/**
 * TODO: Update Description
 * 
 * @package    GameServerControlPanel  
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Controller
{  
    private $_template = '';          
    private $_footerView = 'footer';
    private $_headerView = 'header';
    
    protected function Display($view, $variables = null)
    {                      
        (new View($this->_template . $view, $variables))->display();
    }      
    protected function Render($view, $variables = null)
    {                      
        return (new View($this->_template . $view, $variables))->Render();
    }
    protected function SetTemplate($template)
    {
         if(!Common::StringSuffixIs($template, '/'))
            $template .= '/';
            
         $this->_template = $template;
    }
    protected function SetHeader($header)
    {              
         $this->_headerView = $header;
    }
    protected function SetFooter($footer)
    {              
         $this->_footerView = $footer;
    }
    protected function DisplayLayout($content_view, $content_vars = array(), $header_vars = array(), $footer_vars = array())
    {                 
        $this->Display('layout', array(
            'header'  => $this->Render($this->_headerView, $header_vars),
            'content' => $this->Render($content_view, $content_vars),
            'footer'  => $this->Render($this->_footerView, $footer_vars)
        ));
    }
    
} 