<?php
class Listrak_TransactionalEmail_Model_Cron_Process_Expirations extends ST_MonkeySports_Model_Cron_Process_Expirations
{
    /**
     * Sends an email.
     * Overrding the method to invoke the listrak api.
     * @param      <type>  $customer  The customer
     * @param      <type>  $template  The template
     * @param      <type>  $vars      The variables
     */
    protected function _sendEmail($customer, $template, $vars = null)
    {
    	Mage::getModel("transactionalemail/api")->sendSweetToothEmail($customer, $template, $vars);
    }
}
