<?php

/**
 * Details on a particular contact
 * 
 * @author ilateral
 * @package Contacts
 */
class Contact extends DataObject {
    
    private static $db = array(
        "Salutation" => "Varchar(5)",
        "FirstName" => "Varchar(255)",
		"MiddleName" => "Varchar(255)",
        "Surname" => "Varchar(255)",
        "Company" => "Varchar(255)",
        "Phone" => "Varchar(15)",
        "Mobile" => "Varchar(15)",
        "Email" => "Varchar(255)",
        "Address1" => "Varchar(255)",
        "Address2" => "Varchar(255)",
        "City" => "Varchar(255)",
        "County" => "Varchar(255)",
        "Country" => "Varchar(255)",
        "PostCode" => "Varchar(10)",
        "Tags" => "Varchar(255)",
        "Source" => "Text",
        "Notes" => "Text",
    );
    
	private static $belongs_many_many = array(
		'Lists' => 'ContactList'
	);
    
    private static $summary_fields = array(
        "FirstName",
        "Surname",
        "Email",
        "Address1",
        "Address2",
        "City",
        "PostCode",
        "Tags"
    );

	private static $default_sort = '"FirstName" ASC, "Surname" ASC';
    
	private static $searchable_fields = array(
		"Salutation",
		"FirstName",
		"MiddleName",
		"Surname",
		"Email",
        "Address1",
        "Address2",
        "City",
        "Country",
        "PostCode",
		"Lists.Title"
	);
    
    public function getTitle() {
		$t = '';
		if (!empty($this->Salutation)) $t = "$this->Salutation ";
        $f = '';
		if (!empty($this->FirstName)) $f = "$this->FirstName ";
		$m = '';
		if (!empty($this->MiddleName)) $m = "$this->MiddleName ";
		$s = '';
		if (!empty($this->Surname)) $s = "$this->Surname ";
		$e = '';
		if (!empty($this->Email)) $e = "($this->Email)";
        
		return $s.$f.$m.$s.$e;
	}
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        // Setup tag field
        $tag_field = TagField::create(
            "Tags",
            "Tags",
            null,
            "Contact"
        )->setRightTitle(_t(
            "Contacts.TagDescription",
            "List of tags related to this contact, seperated by a comma."
        ));
        
        $tag_field->setSeparator(",");
        
        $fields->replaceField("Tags", $tag_field);
        
        return $fields;
    }
    
    public function getCMSValidator() {
        return new RequiredFields(array(
            "FirstName",
            "Surname"
        ));
    }
}
