<?php
class Sedo_MarkitUpIntegrator_Route_PrefixAdmin_MarkItUp implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
	        //Please, discover what action the user wants to call!
		$action = $router->resolveActionWithStringParam($routePath, $request, 'miu_button_id');

		return $router->getRouteMatch('Sedo_MarkitUpIntegrator_ControllerAdmin_MarkItUp', $action);
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithStringParam($outputPrefix, $action, $extension, $data, 'miu_button_id');
	}
}

?>