<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *@ORM\Entity
 *@ORM\Table(name="EntiteA")
 */
class EntiteA
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
					
	/**
	 * @ORM\Column(type="boolean", options={"default":true})
	 */
	private $boolTrue;
					
	/**
	 * @ORM\Column(type="string", length=20, nullable=true)
	 */
	private $nomANull;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $nomA;
	
	/**
	* @ORM\ManyToOne(targetEntity="EntiteB")
	* @ORM\JoinColumn(name="EntiteB_id", referencedColumnName="id")
	*/
	private $EntiteB;
					
	public function __construct(){
				
		$this->boolTrue = true;
	}
	/**
	* Get id
	* @return integer
	*/
	public function getid()
	{
		return $this->id;
	}

	/**
	 * @param integer $id
	 * @return integer
	 */
	public function setid($id)
	{
		$this->id = $id;
		return $this;
	}
					
	/**
	* Get boolTrue
	* @return boolean
	*/
	public function getboolTrue()
	{
		return $this->boolTrue;
	}

	/**
	 * @param boolean $boolTrue
	 * @return boolean
	 */
	public function setboolTrue($boolTrue)
	{
		$this->boolTrue = $boolTrue;
		return $this;
	}
					
	/**
	* Get nomANull
	* @return string
	*/
	public function getnomANull()
	{
		return $this->nomANull;
	}

	/**
	 * @param string $nomANull
	 * @return string
	 */
	public function setnomANull($nomANull)
	{
		$this->nomANull = $nomANull;
		return $this;
	}
					
	/**
	* Get nomA
	* @return string
	*/
	public function getnomA()
	{
		return $this->nomA;
	}

	/**
	 * @param string $nomA
	 * @return string
	 */
	public function setnomA($nomA)
	{
		$this->nomA = $nomA;
		return $this;
	}
					
	public function getEntiteB()
	{
		return $this->EntiteB;
	}

	public function setEntiteB($EntiteB)
	{
		$this->EntiteB = $EntiteB;
		return $this;
	}
					
}