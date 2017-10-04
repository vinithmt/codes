<?php

class Monkey_CustomerTier_Adminhtml_CustomertierController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('customer')
		->_addBreadcrumb(Mage::helper('adminhtml')->__("Customer Tier"), Mage::helper('adminhtml')->__("Customer Tier"));
		return $this;
	}   
	
    /**
     * Index action
     */
    public function indexAction()
    {
    	$this->_title($this->__("Customer Tier"));
        $this->_initAction()->renderLayout();
    }

    /**
     * New action
     */
    public function newAction()
    {
     
        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("customertier/customertier")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("customertier_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("customer");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true); 
        $this->_addContent($this->getLayout()->createBlock("customertier/adminhtml_customertier_edit")) ;

        $this->renderLayout();
    }

    /**
     * Edit action
     */
    public function editAction()
    { 

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("customertier/customertier")->load($id);
        if ($model->getId()) {
            Mage::register("customertier_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("customer"); 
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("customertier/adminhtml_customertier_edit")) ;
            $this->renderLayout();
        } 
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("customertier")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    /**
     * Saving or updating action
     */
    public function saveAction()
    {
		$post_data=$this->getRequest()->getPost(); 
       
        if ($post_data) { 

            try { 
                $post_data['tier_min_range'] = str_replace('$', '', $post_data['tier_min_range']);
                $post_data['tier_max_range'] =str_replace('$', '', $post_data['tier_max_range']); 
                $model = Mage::getModel("customertier/customertier")
                ->addData($post_data)
                ->setId($this->getRequest()->getParam("id"))
                ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Customer tier was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setCustomertierData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } 
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setCustomertierData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
            }

        }
        $this->_redirect("*/*/");
    }

    /**
     * Delete action
     */
     
    public function deleteAction()
     {
         if ($id = $this->getRequest()->getParam('id', null)) {
             $model = Mage::getModel('customertier/customertier')->setId($this->getRequest()->getParam('id'))->delete();
             Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customertier')->__('Successfully deleted.'));
             $this->_redirect('*/*/');
             return;
         }
 
         Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customertier')->__('Unable to delete.'));
         $this->_redirect('*/*/');
     }

    /**
     * Delete multiple at the same time
     */
     
     public function massDeleteAction()
     {
         $allIds = $this->getRequest()->getParam('id');
         if(!is_array($allIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customertier')->__('Please select.'));
         } else {
			 foreach ($allIds as $allId) {
				 $sourcecode = Mage::getModel('customertier/customertier')->load($allId);
				 $sourcecode->delete();
             }
             Mage::getSingleton('adminhtml/session')->addSuccess(
                 Mage::helper('customertier')->__('Total of %d record(s) were deleted.', count($allIds))
             );
         }
 
         $this->_redirect('*/*/index');
     }
   
}