<?php

namespace ilateral\SilverStripe\Contacts\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * A container for grouping contacts
 * 
 * @author ilateral
 * @package Contacts
 */
class ContactList extends DataObject implements PermissionProvider
{
    private static $table_name = 'ContactList';

    private static $singular_name = 'List';

    private static $plural_name = 'Lists';

    private static $db = [
        'Title' => "Varchar(255)",
    ];

    private static $many_many = [
        'Contacts' => "ilateral\\SilverStripe\\Contacts\\Model\\Contact",
    ];

    private static $summary_fields = [
        'Title',
        'Contacts.Count'
    ];

    private static $searchable_fields = [
        'Title'
    ];

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
        $fields = parent::getCMSFields();

        // Move contacts field to main tab
        $contacts_field = $fields->dataFieldByName("Contacts");
        $fields->removeByName("Contacts");

        if ($contacts_field) {
            $fields->addFieldToTab(
                'Root.Main',
                $contacts_field
            );
        }
        
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
    
    public function canView($member = false, $context = [])
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

    public function canCreate($member = null, $context = [])
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

    public function canEdit($member = null, $context = [])
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

    public function canDelete($member = null, $context = [])
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
