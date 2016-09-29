<?php

/**
 * Notes on a particular contact
 * 
 * @author ilateral
 * @package Contacts
 */
class Note extends DataObject
{
    
    private static $db = array(
        "Content" => "Text",
        "Flag" => "boolean"
    );
    
    private static $has_one = array(
        "Contact" => "Contact"
    );
    
    private static $casting = array(
        'FlaggedNice' => 'Boolean'
    );
    
    private static $summary_fields = array(
        "FlaggedNice" => "Flagged",
        "Content.Summary" => "Content",
        "Created" => "Created"
    );
    
    public function getFlaggedNice()
    {
        $obj = HTMLText::create();
        $obj->setValue(($this->Flag)? '<span class="red">&#10033;</span>' : '');
        return $obj;
    }
}
