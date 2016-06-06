<?php 

namespace resource\core;

use AbstractDAO;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
//use resource\entidades\Usuario;

class UsuarioDAO extents AbstractDAO{
	
	public function __construct() {
		parent::__construct('resource\entidades\Usuario');
	}
}

?>