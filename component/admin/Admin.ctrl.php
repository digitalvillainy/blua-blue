<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Session;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;

class Admin extends Unicore {
    private $components = ['categories','users','articleList','admin'];
    function init() {
        $this->uni('neoan')
            ->callback($this,'setup')
             ->hook('main', 'admin')
             ->output();
    }

    /**
     * @param Neoan $frame
     */
    function setup($frame){
        Session::admin_restricted();
        foreach ($this->components as $component){
            $frame->vueComponent($component);
        }
    }

}
