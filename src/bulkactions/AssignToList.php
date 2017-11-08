<?php

namespace ilateral\SilverStripe\Contacts\BulkActions;

use SilverStripe\Core\Convert;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\PjaxResponseNegotiator;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormAction;
use ilateral\SilverStripe\Contacts\Model\ContactList;
use Colymba\BulkManager\BulkAction\Handler as BulkActionHandler;

/**
 * Bulk action handler that adds selected records to a list
 * 
 * @author ilateral
 * @package Contacts
 */
class AssignToList extends BulkActionHandler
{
   
    /**
     * RequestHandler allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'index',
        'ListForm',
        'doAddToList'
    );


    /**
     * RequestHandler url => action map
     * @var array
     */
    private static $url_handlers = array(
        'assign/ListForm' => 'ListForm',
        'assign' => 'index'
    );
    
    
    /**
     * Return URL to this RequestHandler
     * @param string $action Action to append to URL
     * @return string URL
     */
    public function Link($action = null)
    {
        return Controller::join_links(
            parent::Link(),
            'assign',
            $action
        );
    }
    
    
    /**
     * Creates and return the editing interface
     * 
     * @return string Form's HTML
     */
    public function index()
    {
        $form = $this->listForm();
        $form->setTemplate('LeftAndMain_EditForm');
        $form->addExtraClass('center cms-content');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm Content');
        
        if ($this->request->isAjax()) {
            $response = new HTTPResponse(
                Convert::raw2json(array( 'Content' => $form->forAjaxTemplate()->getValue() ))
            );
            
            $response->addHeader('X-Pjax', 'Content');
            $response->addHeader('Content-Type', 'text/json');
            $response->addHeader('X-Title', 'SilverStripe - Bulk '.$this->gridField->list->dataClass.' Editing');
            
            return $response;
        } else {
            $controller = $this->getToplevelController();
            return $controller->customise(array( 'Content' => $form ));
        }
    }


    /**
     * Return a form with a dropdown to select the list you want to use
     * 
     * @return Form
     */
    public function ListForm()
    {
        $crumbs = $this->Breadcrumbs();
        
        if ($crumbs && $crumbs->count()>=2) {
            $one_level_up = $crumbs->offsetGet($crumbs->count()-2);
        }
        
        $record_ids = "";
        $query_string = "";
        
        foreach ($this->getRecordIDList() as $id) {
            $record_ids .= $id . ',';
            $query_string .= "records[]={$id}&";
        }
        
        // Cut off the last 2 parts of the string
        $record_ids = substr($record_ids, 0, -1);
        $query_string = substr($query_string, 0, -1);
        
        $form = new Form(
            $this,
            'ListForm',
            $fields = new FieldList(
                HiddenField::create("RecordIDs", "", $record_ids),
                DropdownField::create(
                    "ContactListID",
                    _t("Contacts.ChooseList", "Choose a list"),
                    ContactList::get()->map()
                )->setEmptyString(_t("Contacts.SelectList", "Select a List"))
            ),
            $actions = new FieldList(
                FormAction::create('doAddToList', _t("Contacts.Add", 'Add'))
                    ->setAttribute('id', 'bulkEditingAddToListBtn')
                    ->addExtraClass('ss-ui-action-constructive')
                    ->setAttribute('data-icon', 'accept')
                    ->setUseButtonTag(true),
                    
                FormAction::create('Cancel', _t('GRIDFIELD_BULKMANAGER_EDIT_HANDLER.CANCEL_BTN_LABEL', 'Cancel'))
                    ->setAttribute('id', 'bulkEditingUpdateCancelBtn')
                    ->addExtraClass('ss-ui-action-destructive cms-panel-link')
                    ->setAttribute('data-icon', 'decline')
                    ->setAttribute('href', $one_level_up->Link)
                    ->setUseButtonTag(true)
            )
        );
        
        if ($crumbs && $crumbs->count() >= 2) {
            $form->Backlink = $one_level_up->Link;
        }
        
        // override form action URL back to bulkEditForm
        // and add record ids GET var		
        $form->setAttribute(
            'action',
            $this->Link('ListForm?' . $query_string)
        );

        return $form;
    }

    
    /**
     * Saves the changes made in the bulk edit into the dataObject
     * 
     * @return Redirect 
     */
    public function doAddToList($data, $form)
    {
        $className  = $this->gridField->list->dataClass;
        $singleton  = singleton($className);
        
        $return = array();

        if (isset($data['RecordIDs'])) {
            $ids = explode(",", $data['RecordIDs']);
        } else {
            $ids = array();
        }

        $list_id = (isset($data['ContactListID'])) ? $data['ContactListID'] : 0;
        $list = ContactList::get()->byID($list_id);
        
        try {
            foreach ($ids as $record_id) {
                if ($list_id) {
                    $record = DataObject::get_by_id($className, $record_id);
                    
                    if ($record->hasMethod("Lists")) {
                        $list->Contacts()->add($record);
                        $list->write();
                    }
                    
                    $return[] = $record->ID;
                }
            }
        } catch (\Exception $e) {
            $controller = $this->controller;
            
            $form->sessionMessage(
                $e->getResult()->message(),
                ValidationResult::TYPE_ERROR
            );
                
            $responseNegotiator = new PjaxResponseNegotiator(array(
                'CurrentForm' => function () use (&$form) {
                    return $form->forTemplate();
                },
                'default' => function () use (&$controller) {
                    return $controller->redirectBack();
                }
            ));
            
            if ($controller->getRequest()->isAjax()) {
                $controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
            }
            
            return $responseNegotiator->respond($controller->getRequest());
        }
        
        $controller = $this->getToplevelController();
        $form = $controller->EditForm();
        
        $message = "Added " . count($return) . " contacts to mailing list '{$list->Title}'";
        
        $form->sessionMessage(
            $message,
            ValidationResult::TYPE_GOOD
        );
        
        // Changes to the record properties might've excluded the record from
        // a filtered list, so return back to the main view if it can't be found
        $link = $controller->Link();
        $controller->getRequest()->addHeader('X-Pjax', 'Content');
        return $controller->redirect($link);
    }
}
