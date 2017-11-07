<?php

namespace ilateral\SilverStripe\Contacts\Admin;

use Silverstripe\Admin\ModelAdmin;
use SilverStripe\Forms\CheckboxField;
use Colymba\BulkManager\BulkManager;

/**
 * Management interface for contacts
 * 
 * @author ilateral
 * @package Contacts
 */
class ContactAdmin extends ModelAdmin
{
    
    private static $menu_priority = 8;

    private static $managed_models = array(
        "ilateral\\SilverStripe\\Contacts\\Model\\Contact",
        "ilateral\\SilverStripe\\Contacts\\Model\\ContactTag",
        "ilateral\\SilverStripe\\Contacts\\Model\\ContactList"
    );

    private static $url_segment = 'contacts';

    private static $menu_title = 'Contacts';

    private static $model_importers = array(
        'Contact' => 'SilverStripe\\Dev\\CSVBulkLoader'
    );
    
    public function getSearchContext()
    {
        $context = parent::getSearchContext();

        if ($this->modelClass == 'Contact') {
            $context
                ->getFields()
                ->push(new CheckboxField('q[Flagged]', _t("Contacts.ShowFlaggedOnly", 'Show flagged only')));
        }

        return $context;
    }
    
    public function getList()
    {
        $list = parent::getList();

        // use this to access search parameters
        $params = $this->request->requestVar('q');

        if ($this->modelClass == 'Contact' && isset($params['Flagged']) && $params['Flagged']) {
            $list = $list->filter(
                "Notes.Flag",
                true
            );
        }

        return $list;
    }
    
    public $showImportForm = array('Contact');
       
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        
        $class = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($class);
        $config = $gridField->getConfig();

        // Add bulk editing to gridfield
        $manager = new BulkManager();
        $manager->removeBulkAction("unLink");
        
        if ($class == 'Contact') {
            $manager->addBulkAction(
                "assign",
                _t("Contacts.AssignToList", "Assign to list"),
                "BulkActionAssignToList",
                array(
                    'isAjax' => false,
                    'icon' => 'pencil',
                    'isDestructive' => false
                )
            );
        } else {
            $config
                ->removeComponentsByType("GridFieldExportButton")
                ->removeComponentsByType("GridFieldPrintButton");
        }
        
        $config->addComponents($manager);
        
        $this->extend("updateEditForm", $form);
        
        return $form;
    }
}
