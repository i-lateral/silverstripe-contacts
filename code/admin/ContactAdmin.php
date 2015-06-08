<?php

/**
 * Management interface for contacts
 * 
 * @author ilateral
 * @package Contacts
 */
class ContactAdmin extends ModelAdmin {
    
    private static $menu_priority = 8;

    private static $managed_models = array(
        'Contact',
        "ContactList"
    );

    private static $url_segment = 'contacts';

    private static $menu_title = 'Contacts';

    private static $model_importers = array(
        'Contact' => 'CSVBulkLoader'
    );
    
    public $showImportForm = array('Contact');
       
    public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm($id, $fields);
		
		$class = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($class);
        $config = $gridField->getConfig();

        // Add bulk editing to gridfield
        $manager = new GridFieldBulkManager();
        $manager->removeBulkAction("unLink");
        
        if($class == 'Contact') {
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
