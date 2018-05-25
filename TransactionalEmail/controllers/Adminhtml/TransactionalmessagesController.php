<?php
/**
 * Listrak Transactional Email Magento Extension
 *
 * @category  Listrak
 * @package   Listrak_TransactionalEmail
 * @author    MonkeySports Team <vinith.thaithara@monkeysports.com>
 * @copyright 2017 MonkeySports, Inc
 * #1877
 */
class Listrak_TransactionalEmail_Adminhtml_TransactionalmessagesController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('remarketing')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__("Transactional Messages"), Mage::helper('adminhtml')->__("Transactional Messages"));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__("Transactional Messages"));

        $this->_initAction()->renderLayout();
    }

    /**
     * New action
     */
    public function newAction()
    {

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("transactionalemail/transactionalmessages")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("transactionalmessages_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("remarketing");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock("transactionalemail/adminhtml_transactionalmessages_edit"))
            ->_addLeft($this->getLayout()->createBlock('transactionalemail/adminhtml_transactionalmessages_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Edit action
     */
    public function editAction()
    {

        $id             = $this->getRequest()->getParam("id");
        $model          = Mage::getModel("transactionalemail/transactionalmessages")->load($id);
        $attributeModel = Mage::getModel("transactionalemail/attributes")->getCollection()
            ->addFieldToFilter('transactional_message_id', $id)
            ->getItems();
        if ($model->getId()) {
            Mage::register("transactionalmessages_data", $model);
            Mage::register("attributes_data", $attributeModel);
            $this->loadLayout();
            $this->_setActiveMenu("remarketing");
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock("transactionalemail/adminhtml_transactionalmessages_edit"))
                ->_addLeft($this->getLayout()->createBlock('transactionalemail/adminhtml_transactionalmessages_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    /**
     * Saving or updating action
     */
    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();

        if ($post_data) {
            try {
                 
                $model = Mage::getModel("transactionalemail/transactionalmessages")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();
                $transactionalMessageId = $model->getId();
                if (is_array($post_data['mulitpleField'])) {

                     Mage::getModel("transactionalemail/attributes")->deleteAttributeFields($transactionalMessageId);
                    $attributeModel = Mage::getModel("transactionalemail/attributes");

              
                    foreach ($post_data['mulitpleField'] as $key => $value) {
                        $attributeElement = json_decode($value['attribute']['elements']);
                        $attributeModel->setInputField($value['mapfield']);
                        $attributeModel->setAttributeId($attributeElement->attribute_id);
                        $attributeModel->setHeaderId($attributeElement->header_id);
                        $attributeModel->setName($attributeElement->name);
                        $attributeModel->setDataType($attributeElement->datat_ype);
                        $attributeModel->setMaxLength($attributeElement->max_length);
                        $attributeModel->setTransactionalMessageId($transactionalMessageId);
                        $attributeModel->save();
                        $attributeModel->unsetData();

                    }
                }

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Transactional Message was successfully saved."));
                Mage::getSingleton("adminhtml/session")->setCustomertierData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setTransactionalMessagesData($this->getRequest()->getPost());
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
            $model = Mage::getModel('transactionalemail/transactionalmessages')->setId($id)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Successfully deleted.'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Delete multiple at the same time
     */

    public function massDeleteAction()
    { 
        $allIds = $this->getRequest()->getParam('id'); 
        if ($allIds[count($allIds)-1] == "") {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select one or more item.'));
        } else {
            foreach ($allIds as $allId) {
                $sourcecode = Mage::getModel('transactionalemail/transactionalmessages')->load($allId);
                $sourcecode->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($allIds))
            );
        }

        $this->_redirect('*/*/index');
    }

  

    /**
     * massStatusAction
     */
    public function massStatusAction()
    {
  
        $allIds = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');
       

        if ($allIds[count($allIds)-1] == "") {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select one or more item.'));
        } else {
            foreach ($allIds as $allId) {
                $sourcecode = Mage::getModel('transactionalemail/transactionalmessages')->load($allId);
                $sourcecode->setIsActive($status);
                $sourcecode->save();
            }
            $status = (($status == 1) ? 'enabled' : 'disabled');
            Mage::getSingleton('adminhtml/session')->addSuccess(
               
                Mage::helper('adminhtml')->__('Total of %d record(s) were '. $status.'.', count($allIds))
            );
        }

        $this->_redirect('*/*/index');
    }


    public function getListrakAttributesAction()
    {

        $this->getResponse()->setHeader('Content-type', 'application/json');
        try {
            $attributeObject = Mage::getModel('transactionalemail/api')->getAttributes();

            $html = '';
            foreach ($attributeObject as $key => $value) {

                $html .= '<optgroup label="' . $value->Name . '"></optgroup>';
                foreach ($value->WSProfileAttributes as $keyAttributes => $valueAttributes) {
                    $html .= '<option
                                data-attribute_id="' . $valueAttributes->AttributeID . '"
                                data-name="' . $valueAttributes->Name . '"
                                data-header_id="' . $valueAttributes->HeaderID . '"
                                data-data_type="' . $valueAttributes->DataType . '"
                                data-max_length="' . $valueAttributes->MaxLength . '"
                                data-position="' . $valueAttributes->Position . '"
                                value="' . $valueAttributes->AttributeID . '">&nbsp;&nbsp;&nbsp;'.$valueAttributes->Name.'/'.$value->Name.'</option>';
                }
                $html .= '</optgroup>';
            }
            $jsonData = json_encode(array('data' => $html));
            if ($html == '') {
                $jsonData = array('error' => 'Attributes not found in this store.');
            }

            $this->getResponse()->setBody($jsonData);

        } catch (Exception $e) {
            $jsonData = json_encode(array('error' => $e->getMessage()));
            $this->getResponse()->setBody($jsonData);
        }
    }
}
