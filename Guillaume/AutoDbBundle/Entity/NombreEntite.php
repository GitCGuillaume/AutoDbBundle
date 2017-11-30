<?php
// src/Guillaume/AutoDbBundle/Entity/NombreEntite.php
namespace Guillaume\AutoDbBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class NombreEntite
{
	/**
    * @Assert\NotNull()
 	* @Assert\Type("integer")
    */
	private $nombreEntite;

	/**
     * Get nombreEntite
     *
     * @return integer
     */
	public function getNombreEntite(){
		return $this->nombreEntite;
	}

	/**
	* Set nombreEntite
	*
	* @param integer $nombreEntite
	*
	* @return Entite
	*/
	public function setNombreEntite($nombreEntite){
		$this->nombreEntite = $nombreEntite;
		return $this;
	}
}
?>
