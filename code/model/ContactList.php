<?php

/**
 * A container for grouping contacts
 * 
 * @author ilateral
 * @package Contacts
 */
class ContactList extends DataObject implements PermissionProvider
{

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

    public function fieldLabels($includelrelations = true)
    {
        $labels = parent::fieldLabels($includelrelations);
        $labels["Title"] = _t('Contacts.FieldTitle', "Title");
        $labels["FullTitle"] = _t('Contacts.FieldTitle', "Title");
        $labels["ActiveRecipients.Count"] = _t('Contacts.Recipients', "Recipients");
        return $labels;
    }

    public function getCMSFields()
    {
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
    
    public function providePermissions()
    {
        return array(
            "CONTACTS_LISTS_MANAGE" => array(
                'name' => _t(
                    'Contacts.PERMISSION_MANAGE_CONTACTS_LISTS_DESCRIPTION',
                    'Manage contact lists'
                ),
                'help' => _t(
                    'Contacts.PERMISSION_MANAGE_CONTACTS_LISTS_HELP',
                    'Allow creation and editing of contact lists'
                ),
                'category' => _t('Contacts.Contacts', 'Contacts')
            ),
            "CONTACTS_LISTS_DELETE" => array(
                'name' => _t(
                    'Contacts.PERMISSION_DELETE_CONTACTS_LISTS_DESCRIPTION',
                    'Delete contact lists'
                ),
                'help' => _t(
                    'Contacts.PERMISSION_DELETE_CONTACTS_LISTS_HELP',
                    'Allow deleting of contact lists'
                ),
                'category' => _t('Contacts.Contacts', 'Contacts')
            )
        );
    }
    
    public function canView($member = false)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }
        
        if ($member instanceof Member) {
            $memberID = $member->ID;
        } elseif (is_numeric($member)) {
            $memberID = $member;
        } else {
            $memberID = Member::currentUserID();
        }
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_LISTS_MANAGE"))) {
            return true;
        }

        return false;
    }

    public function canCreate($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }
        
        if ($member instanceof Member) {
            $memberID = $member->ID;
        } elseif (is_numeric($member)) {
            $memberID = $member;
        } else {
            $memberID = Member::currentUserID();
        }
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_LISTS_MANAGE"))) {
            return true;
        }

        return false;
    }

    public function canEdit($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }
        
        if ($member instanceof Member) {
            $memberID = $member->ID;
        } elseif (is_numeric($member)) {
            $memberID = $member;
        } else {
            $memberID = Member::currentUserID();
        }
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_LISTS_MANAGE"))) {
            return true;
        }

        return false;
    }

    public function canDelete($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);

        if ($extended !== null) {
            return $extended;
        }
        
        if ($member instanceof Member) {
            $memberID = $member->ID;
        } elseif (is_numeric($member)) {
            $memberID = $member;
        } else {
            $memberID = Member::currentUserID();
        }
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_LISTS_DELETE"))) {
            return true;
        }

        return false;
    }
}
