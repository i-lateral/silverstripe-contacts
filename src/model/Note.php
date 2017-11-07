<?php

namespace ilateral\SilverStripe\Contacts\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText as HTMLText;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\TagField\TagField;
use ilateral\SilverStripe\Contacts\Model\ContactTag;

/**
 * Notes on a particular contact
 * 
 * @author ilateral
 * @package Contacts
 */
class Note extends DataObject
{

    private static $db = [
        "Content" => "Text",
        "Flag" => "Boolean"
    ];
    
    private static $has_one = [
        "Contact" => "ilateral\\SilverStripe\\Contacts\\Model\\Contact"
    ];
    
    private static $casting = [
        'FlaggedNice' => 'Boolean'
    ];
    
    private static $summary_fields = [
        "FlaggedNice" => "Flagged",
        "Content.Summary" => "Content",
        "Created" => "Created"
    ];
    
    /**
     * Has this note been flagged? If so, return a HTML Object that
     * can be loaded into a gridfield.
     *
     * @return DBHTMLText
     */
    public function getFlaggedNice()
    {
        $obj = HTMLText::create();
        $obj->setValue(($this->Flag)? '<span class="red">&#10033;</span>' : '');
        return $obj;
    }
}
