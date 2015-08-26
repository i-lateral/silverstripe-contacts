<?php

/**
 * Details on a particular contact
 * 
 * @author ilateral
 * @package Contacts
 */
class Contact extends DataObject implements PermissionProvider {
    
    private static $db = array(
        "Salutation" => "Varchar(20)",
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
        "Source" => "Text",
        "Notes" => "Text",
    );
    
	private static $many_many = array(
		'Tags' => 'ContactTag'
	);

	private static $belongs_many_many = array(
		'Lists' => 'ContactList'
	);
    
    private static $casting = array(
		'TagsList' => 'Varchar'
	);
    
    private static $summary_fields = array(
        "FirstName",
        "Surname",
        "Email",
        "Address1",
        "Address2",
        "City",
        "PostCode",
        "TagsList"
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
        "Tags.Title",
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
    
    public function getTagsList() {
        $return = "";
        $tags = $this->Tags();
        $i = 1;
        
        foreach($tags as $tag) {
            $return .= $tag->Title;
            
            if($i < $tags->count())
                $return .= ", ";
            
            $i++;
        }
        
        return $return;
    }
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $fields->removeByName("Tags");
        
        $tag_field = TagField::create(
            'Tags',
            null,
            ContactTag::get(),
            $this->Tags()
        )->setRightTitle(_t(
            "Contacts.TagDescription",
            "List of tags related to this contact, seperated by a comma."
        ))->setShouldLazyLoad(true);
        
        $fields->addFieldToTab(
            "Root.Main",
            $tag_field
        );
        
        return $fields;
    }
    
    public function getCMSValidator() {
        return new RequiredFields(array(
            "FirstName",
            "Surname"
        ));
    }
    
	public function providePermissions() {
		return array(
			"CONTACTS_MANAGE" => array(
				'name' => _t(
					'Contacts.PERMISSION_MANAGE_CONTACTS_DESCRIPTION',
					'Manage contacts'
				),
				'help' => _t(
					'Contacts.PERMISSION_MANAGE_CONTACTS_HELP',
					'Allow creation and editing of contacts'
				),
				'category' => _t('Contacts.Contacts', 'Contacts')
			),
            "CONTACTS_DELETE" => array(
				'name' => _t(
					'Contacts.PERMISSION_DELETE_CONTACTS_DESCRIPTION',
					'Delete contacts'
				),
				'help' => _t(
					'Contacts.PERMISSION_DELETE_CONTACTS_HELP',
					'Allow deleting of contacts'
				),
				'category' => _t('Contacts.Contacts', 'Contacts')
			)
		);
	}
    
    public function canView($member = false) {
        $extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}
        
        if($member instanceof Member)
            $memberID = $member->ID;
        else if(is_numeric($member))
            $memberID = $member;
        else
            $memberID = Member::currentUserID();
            
        if($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_MANAGE")))
            return true;

        return false;
    }

    public function canCreate($member = null) {
        $extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}
        
        if($member instanceof Member)
            $memberID = $member->ID;
        else if(is_numeric($member))
            $memberID = $member;
        else
            $memberID = Member::currentUserID();
            
        if($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_MANAGE")))
            return true;

        return false;
    }

    public function canEdit($member = null) {
        $extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}
        
        if($member instanceof Member)
            $memberID = $member->ID;
        else if(is_numeric($member))
            $memberID = $member;
        else
            $memberID = Member::currentUserID();
            
        if($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_MANAGE")))
            return true;

        return false;
    }

    public function canDelete($member = null) {
        $extended = $this->extendedCan(__FUNCTION__, $member);

		if($extended !== null) {
			return $extended;
		}
        
        if($member instanceof Member)
            $memberID = $member->ID;
        else if(is_numeric($member))
            $memberID = $member;
        else
            $memberID = Member::currentUserID();
            
        if($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_DELETE")))
            return true;

        return false;
    }
}
