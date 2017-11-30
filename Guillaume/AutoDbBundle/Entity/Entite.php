<?php
// src/Guillaume/AutoDbBundle/Entity/NombreEntite.php
namespace Guillaume\AutoDbBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class Entite
{
	private $nomEntite;

		/**
     * Get nomEntite
     *
     * @return integer
     */
	public function getNomEntite(){
		return $this->nomEntite;
	}

	/**
	* Set nombreEntite
	*
	* @param integer $nombreEntite
	*
	* @return Entite
	*/
	public function setNombreEntite($nomEntite){
		$this->nomEntite = $nomEntite;
		return $this;
	}
}

?>