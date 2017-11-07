<?php

namespace ilateral\SilverStripe\Contacts\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * A tag for keyword descriptions of a contact.
 *
 * @package silverstripe
 * @subpackage contacts
 */
class ContactTag extends DataObject implements PermissionProvider
{
    
    private static $singular_name = 'Tag';

    private static $plural_name = 'Tags';
        
    private static $db = array(
        'Title' => 'Varchar(255)',
    );

    private static $belongs_many_many = [
        'Contacts' => 'ilateral\\SilverStripe\\Contacts\\Model\\Contact',
    ];
    
    private static $summary_fields = [
        'Title',
        'Contacts.Count'
    ];

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

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }
    
    public function providePermissions()
    {
        return array(
            "CONTACTS_TAGS_MANAGE" => array(
                'name' => _t(
                    'Contacts.PERMISSION_MANAGE_CONTACTS_TAGS_DESCRIPTION',
                    'Manage contact tags'
                ),
                'help' => _t(
                    'Contacts.PERMISSION_MANAGE_CONTACTS_TAGS_HELP',
                    'Allow creation and editing of contact lists'
                ),
                'category' => _t('Contacts.Contacts', 'Contacts')
            ),
            "CONTACTS_TAGS_DELETE" => array(
                'name' => _t(
                    'Contacts.PERMISSION_DELETE_CONTACTS_TAGS_DESCRIPTION',
                    'Delete contact lists'
                ),
                'help' => _t(
                    'Contacts.PERMISSION_DELETE_CONTACTS_TAGS_HELP',
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
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_TAGS_MANAGE"))) {
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
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_TAGS_MANAGE"))) {
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
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_TAGS_MANAGE"))) {
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
            
        if ($memberID && Permission::checkMember($memberID, array("ADMIN", "CONTACTS_TAGS_DELETE"))) {
            return true;
        }

        return false;
    }
}
