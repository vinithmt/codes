<?php
class Monkey_Geolocation_Model_Nocache extends Enterprise_PageCache_Model_Container_Abstract
{
	 
	protected function _getIdentifier()
	{
	return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
	}
	 
	protected function _getCacheId()
	{
		return 'geolocation_nocache' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier().time());
	} 

	protected function _renderBlock()
	{
		$blockClass = $this->_placeholder->getAttribute('block');
		$template = $this->_placeholder->getAttribute('template');
		 
		$block = new $blockClass;
		$block->setTemplate($template);
		$block->setCacheLifetime(false);
		return $block->toHtml();
	}
	  /**
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null $lifetime
     * @return bool|Enterprise_PageCache_Model_Container_Abstract
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        return false;
    }
}
