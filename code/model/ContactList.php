<?php

/**
 * A container for grouping contacts
 * 
 * @author ilateral
 * @package Contacts
 */
class ContactList extends DataObject {

    private static $singular_name = 'List';

	private static $plural_name = 'Lists';

	private static $db = array(
		'Title' => "Varchar(255)",
	);

	private static $many_many = array(
		'Contacts' => "Contact",
	);

	private static $summary_fields = array(
		'Title',
		'Contacts.Count'
	);

	private static $searchable_fields = array(
		'Title'
	);

	public function fieldLabels($includelrelations = true) {
		$labels = parent::fieldLabels($includelrelations);
		$labels["Title"] = _t('Contacts.FieldTitle', "Title");
		$labels["FullTitle"] = _t('Contacts.FieldTitle', "Title");
		$labels["ActiveRecipients.Count"] = _t('Contacts.Recipients', "Recipients");
		return $labels;
	}

	function getCMSFields() {
		$fields = new FieldList();
        
		$fields->push(
            new TabSet(
                "Root",
                $mainTab = new Tab("Main")
            )
        );

		$fields->addFieldToTab(
            'Root.Main',
			new TextField('Title')
        );

		$grid_config = GridFieldConfig::create(
            new GridFieldButtonRow('before'),
            new GridFieldToolbarHeader(),
            new GridFieldAddNewButton('toolbar-header-left'),
            $autocompelete = new GridFieldAutocompleterWithFilter(
                'toolbar-header-right',
                array(
                    'FirstName',
                    'MiddleName',
                    'Surname',
                    'Email',
                )
            ),
            new GridFieldSortableHeader(),
            $dataColumns = new GridFieldDataColumns(),
            new GridFieldFilterHeader(),
            new GridFieldDeleteAction(true),
            new GridFieldPaginator(50),
            new GridFieldDetailForm(),
            new GridFieldEditButton()
        );

		$fields->addFieldToTab(
            'Root.Main',
            $contacts_grid = GridField::create(
                'Contacts',
                "",
                $this->Contacts(),
                $grid_config
            )
        );
        
		$this->extend("updateCMSFields", $fields);

		return $fields;
	}

}
