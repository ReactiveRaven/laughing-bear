<?php

namespace RRaven\Bundle\LaughingbearBundle\Entity;

abstract class AbstractContainerAwareEntity {
	private $_container;
	
	protected function getContainer() {
		global $kernel;
		
		return 
			(
				$this->_container
					? $this->_container
					: $this->_container = $kernel->getContainer()
			)
		;
	}
}
