<?Php
namespace resource\entidades;


/**
 * @Entity
 * @Table(name="usuario")
 */
class Usuario{

	/**
	 * @var integer @Id
	 * @Column(name="id", type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */	
	private $id;

	/**
	 * @var string @Column(type="string", length=255)
	 */
	private $email = "";

	/**
	 * @var string @Column(type="string", length=255)
	 */
	private $senha = "";

	/**
	 * @var string @Column(type="string", length=255)
	 */
	private $endereco = "";

	public function __construct($id = 0,$email= "" ,$senha= "" ,$endereco = 0){
		$this->id = $id;
		$this->email = $email;
		$this->senha = $senha;
		$this->endereco = $endereco;

	}

	public static function construct($array){
		$obj = new Usuario();
		$obj->setId( $array['id']);
		$obj->setEmail( $array['email']);
		$obj->setSenha( $array['senha']);
		$obj->setEndereco( $array['endereco']);
		return $obj;

	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id=$id;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email=$email;
	}

	public function getSenha(){
		return $this->senha;
	}

	public function setSenha($senha){
		$this->senha=$senha;
	}

	public function getEndereco(){
		return $this->endereco;
	}

	public function setEndereco($endereco){
		$this->endereco=$endereco;
	}

	public function equals($object){
		if($object instanceof Usuario){

			if($this->id!=$object->id){
				return false;

			}

			if($this->email!=$object->email){
				return false;

			}

			if($this->senha!=$object->senha){
				return false;

			}

			if($this->endereco!=$object->endereco){
				return false;

			}

			return true;

		}
		else{
			return false;
		}
	}

	public function toString(){

		return "  [id:" .$this->id. "]  [email:" .$this->email. "]  [senha:" .$this->senha. "]  [endereco:" .$this->endereco. "]  " ;
	}

}
