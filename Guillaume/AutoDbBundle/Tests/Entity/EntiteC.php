<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *@ORM\Entity
 *@ORM\Table(name="EntiteC")
 */
class EntiteC
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
					
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $boolC;
					
	/**
 	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $boolCNull;
					
	/**
	 * @ORM\Column(type="boolean", nullable=true, options={"default":true})
	 */
	private $boolTrueNull;
					
	/**
	 * @ORM\Column(type="boolean", options={"default":true})
	 */
	private $boolTrue;
					
	/**
	* @ORM\ManyToOne(targetEntity="EntiteD")
	* @ORM\JoinColumn(name="EntiteD_id", referencedColumnName="id")
	*/
	private $EntiteD;
					
	public function __construct(){
				
		$this->boolC = false;
		$this->boolCNull = false;
		$this->boolTrueNull = true;
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
	* Get boolC
	* @return boolean
	*/
	public function getboolC()
	{
		return $this->boolC;
	}

	/**
	 * @param boolean $boolC
	 * @return boolean
	 */
	public function setboolC($boolC)
	{
		$this->boolC = $boolC;
		return $this;
	}
					
	/**
	* Get boolCNull
	* @return boolean
	*/
	public function getboolCNull()
	{
		return $this->boolCNull;
	}

	/**
	 * @param boolean $boolCNull
	 * @return boolean
	 */
	public function setboolCNull($boolCNull)
	{
		$this->boolCNull = $boolCNull;
		return $this;
	}
					
	/**
	* Get boolTrueNull
	* @return boolean
	*/
	public function getboolTrueNull()
	{
		return $this->boolTrueNull;
	}

	/**
	 * @param boolean $boolTrueNull
	 * @return boolean
	 */
	public function setboolTrueNull($boolTrueNull)
	{
		$this->boolTrueNull = $boolTrueNull;
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
					
	public function getEntiteD()
	{
		return $this->EntiteD;
	}

	public function setEntiteD($EntiteD)
	{
		$this->EntiteD = $EntiteD;
		return $this;
	}
					
}