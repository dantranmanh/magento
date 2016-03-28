<?php
class Korifi_CommWeb_Model_CommWeb_PaymentAction
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => 'authorize_capture',
				'label' => 'Authorise and Capture'
			),
			array(
				'value' => 'authorize',
				'label' => 'Authorise'
			)
		);
	}
}

?>
