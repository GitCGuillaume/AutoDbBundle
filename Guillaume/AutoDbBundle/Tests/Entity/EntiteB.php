<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *@ORM\Entity
 *@ORM\Table(name="EntiteB")
 */
class EntiteB
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
					
	/**
	 * @ORM\Column(type="text", nullable=true)
	*/
	private $textNull;
	
	/**
	 * @ORM\Column(type="text")
	 */
	private $textNotNull;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $boolEntiteB;
					
	/**
	 * @ORM\ManyToMany(targetEntity="EntiteC")
	 * @ORM\JoinTable(name="EntiteB_EntiteC",
	 *	joinColumns={@ORM\JoinColumn(name="EntiteB_id", referencedColumnName="id")},
	 *	inverseJoinColumns={@ORM\JoinColumn(name="EntiteC_id", referencedColumnName="id")}
	 *	)
	 */
	private $EntiteC;
						
	/**
	* @ORM\ManyToOne(targetEntity="EntiteD")
	* @ORM\JoinColumn(name="EntiteD_id", referencedColumnName="id")
	*/
	private $EntiteD;
					
	public function __construct(){
				
		$this->boolEntiteB = false;
		$this->EntiteC = new ArrayCollection();
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
	* Get textNull
	* @return text
	*/
	public function gettextNull()
	{
		return $this->textNull;
	}

	/**
	 * @param text $textNull
	 * @return text
	 */
	public function settextNull($textNull)
	{
		$this->textNull = $textNull;
		return $this;
	}
					
	/**
	* Get textNotNull
	* @return text
	*/
	public function gettextNotNull()
	{
		return $this->textNotNull;
	}

	/**
	 * @param text $textNotNull
	 * @return text
	 */
	public function settextNotNull($textNotNull)
	{
		$this->textNotNull = $textNotNull;
		return $this;
	}
					
	/**
	* Get boolEntiteB
	* @return boolean
	*/
	public function getboolEntiteB()
	{
		return $this->boolEntiteB;
	}

	/**
	 * @param boolean $boolEntiteB
	 * @return boolean
	 */
	public function setboolEntiteB($boolEntiteB)
	{
		$this->boolEntiteB = $boolEntiteB;
		return $this;
	}
					
	/**
	 * Add EntiteC
	 * @param \AppBundle\Entity\EntiteC $EntiteC
	 * @return EntiteB
	 */
	public function addEntiteC(\AppBundle\Entity\EntiteC $EntiteC)
	{
		$this->EntiteC[] = $EntiteC;
		return $this;
	}

	/**
	 * Remove EntiteC
	 * @param \AppBundle\Entity\EntiteC $EntiteC
	 */
	public function removeEntiteC(\AppBundle\Entity\EntiteC $EntiteC)
	{
		$this->EntiteC->removeElement($EntiteC);
	}

	/**
	 * Get EntiteC
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getEntiteC()
	{
		return $this->EntiteC;
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