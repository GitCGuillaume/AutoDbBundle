<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *@ORM\Entity
 *@ORM\Table(name="EntiteD")
 */
class EntiteD
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
					
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $stringNull;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $stringNotNull;
	
	/**
	 * @ORM\OneToOne(targetEntity="EntiteA")
	 * @ORM\JoinColumn(name="EntiteA_id", referencedColumnName="id")
	 */
	private $EntiteA;
							
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
	* Get stringNull
	* @return string
	*/
	public function getstringNull()
	{
		return $this->stringNull;
	}

	/**
	 * @param string $stringNull
	 * @return string
	 */
	public function setstringNull($stringNull)
	{
		$this->stringNull = $stringNull;
		return $this;
	}
					
	/**
	* Get stringNotNull
	* @return string
	*/
	public function getstringNotNull()
	{
		return $this->stringNotNull;
	}

	/**
	 * @param string $stringNotNull
	 * @return string
	 */
	public function setstringNotNull($stringNotNull)
	{
		$this->stringNotNull = $stringNotNull;
		return $this;
	}
					
}