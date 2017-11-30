<?php

namespace Guillaume\AutoDbBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class AutomatingController extends Controller{
	public function GetEntityAction(Request $request){
		//On prend la clé et on affiche leur valeur
        if ($request->isMethod('POST')){
        	if ($this->isCsrfTokenValid('token_id', $request->request->get('_csrf_token'))) {
        		$listeCardinalite = ["1,n", "0,n", "1,1", "0,1"];
                $nbKeys = count($request->request->keys());
		        
		        //Variables pour getAttribut
		        $postKey = array();
		        $postValue = array();

				//variables dynamiques
				$listeNomEntite = array();
				$firstNameEntity = $request->request->keys()[0];

				//Supprime la fin du nom de la clé
				
				$removeX = substr($firstNameEntity, 0
					, strpos( $firstNameEntity, "_x"));

				//Enregistre le nom de la première entité
				array_push($listeNomEntite, $removeX);
				echo "<br>";
				for($i = 0; $i < $nbKeys; $i++){
					$key = $request->request->keys()[$i];
					$name = $request->request->get($key);
					if($name == ""){
		                $this->get('session')->getFlashBag()->clear();
		                $this->addFlash(
		                    'error',
		                    'Les balises ne doivent être vides.'
		                );
		                return $this->redirectToRoute('guillaume_auto_db_attribut');
					}

					array_push($postKey, $key);
					array_push($postValue, $name);

					$findAssociation = strstr($request->request->keys()[$i], "Association");
					//On vérifie si la clé est bien une association, et si la suivante l'est aussi, si la suivant n'en est pas une, alors on enregistre la clé suivante dans le array listeNomEntite
					if($findAssociation && !strstr($request->request->keys()[$i+1], "Association")){
						$nextName = $request->request->keys()[$i+1];
						if($nextName != "_csrf_token" && strstr($nextName, "_x")){
							$nextName = $request->request->keys()[$i+1];
							$word = substr($nextName, 0, strpos( $nextName, "_x"));
							array_push($listeNomEntite, $word);
						}
					}
				}

				//Function setAttribute()
				$setAttribute = $this->setAttribute($postKey, $postValue,$listeNomEntite);
				//Function setCheckbox()
				$setCheckBox = $this->setCheckBox($postKey, $postValue,$listeNomEntite);
				//Function setAssociation()
				$setAssociation = $this->setAssociation($postKey, $postValue,$listeNomEntite);


				//COMMENCEMENT DU DEVELLOPEMENT DE LA CRÉATION ET DE L'ÉCRITURE DES DIFFERENTS FICHIERS
				//FWRITE EST SENSIBLE À LA CASE ET POUR CHAQUE LIGNE ÉCRITE LE POINTEUR COMMENCE À LA COLONNE 1.
				//http://php.net/manual/fr/function.fwrite.php

				//La boucle $j - $listeNomEntite permet de faire la différence entre chaque entité,
				//c'est elle qui va notamment permettre de séparer chaque fichier

				//Variable qui stock chaque fichier crée
				$stockHandle = array();
				$zip = new \ZipArchive();
				$zipPath = sys_get_temp_dir()."/autoDb.zip";
				if($zipPath === false){
					$this->get('session')->getFlashBag()->clear();
			        $this->addFlash(
			            'error',
			            "Le chemin n'a pu être trouvé, vérifiez les droits d'utilisateurs."
			        );
			        return $this->redirectToRoute('guillaume_auto_db_attribut');
				}
				$zipExist = "autoDb.zip";
				if(file_exists($zipExist)){
					$this->get('session')->getFlashBag()->clear();
			        $this->addFlash(
			            'error',
			            "autoDb.zip existe déjà, pour créer une nouvelle archive, renommez ou supprimez celle déjà existante.
			            Emplacement : $zipExist"
			        );
			        return $this->redirectToRoute('guillaume_auto_db_attribut');
				}
				$zipCreate = $zip->open($zipPath, \ZipArchive::CREATE);
				for($j = 0; $j < count($listeNomEntite); $j++){
					//$construct enregistre chaque attribut ayant une association
					$construct = array();
					//$temp_file = tempnam('/home/guillaume/test/', $listeNomEntite[$j]);
					//On vérifie si le fichier temporaire a les autorisations en écriture, attention à bien vérifier les droits de lecture après la création du fichier.
			
					//http://php.net/manual/fr/function.fopen.php
					$filePath = sys_get_temp_dir()."/".$listeNomEntite[$j].'.php';
					$handle = fopen($filePath, "a+") or die("Impossible d'ouvrir le fichier");
					//http://php.net/manual/fr/function.fwrite.php
					if(isset($_POST['directoryName'])){
						fwrite($handle, '<?php
namespace '.$request->request->get("bundleName").'\\'.$request->request->get("directoryName").'\Entity;
use Doctrine\ORM\Mapping as ORM;');
					}else{
						fwrite($handle, '<?php
namespace '.$request->request->get("bundleName").'\\Entity;
use Doctrine\ORM\Mapping as ORM;');
												}
						//Permet de vérifier si une association existe bien pour lentité
					if(!empty($setAssociation)){
						fwrite($handle, '
use Doctrine\Common\Collections\ArrayCollection;');
					}
					fwrite($handle, '
/**
 *@ORM\Entity
 *@ORM\Table(name="'.$listeNomEntite[$j].'")
 */
class '.$listeNomEntite[$j].'
{');




	for($k = 0; $k < count($setAttribute); $k++){
		//On vérifie si l'attribut sélectionné est bien égale au nom de l'entité définie
		if($setAttribute[$k] === $listeNomEntite[$j]){
			$checkEntite = $listeNomEntite[$j];
			
			//L'array checkTypeBox enregistre les 2 premiers caractères de chaque checkbox enregistré via la fonction setCheckBox()
			$checkTypeBox = array();
			
			for($l = 0; $l < count($setCheckBox); $l++){
				//setAttribute[?=>"EntiteA"] == $setCheckBox[?=>"EntiteA"]
				//&& $setAttribute[?+1=>"idA" == $setCheckBox[?+2=>"idA"]]
				//Permet d'obtenir les types de checkbox sélectionnés
				if($setAttribute[$k] == $setCheckBox[$l] && $setAttribute[$k+1] == $setCheckBox[$l+2]){
					array_push($checkTypeBox, substr($setCheckBox[$l+1], 0, 2));
					$checkEntite = $setAttribute[$k+1];
				}

			}
			//Le switch fait la différence entre chaque type d'attribut qui se trouve après le nom de l'attribut dans l'array $setAttribute(string, integer...)
			switch ($setAttribute[$k+2]) {
				//<---------------------------------------------------------------------------------------------------------------------------
				case "string":
					$length = $request->request->get($setAttribute[$k+3]);
					//Pour string la première clé doit être null, sinon la colonne non null sera choisit (type=string, ...)
					if(isset($checkTypeBox[0]) == "nL" 
					&& $checkEntite != $listeNomEntite[$j])
					{
							fwrite($handle, '
	/**
	 * @ORM\\Column(type="string", length='.$length.', nullable=true)
	 */
	private $'.$setAttribute[$k+1].';
	');
					}else{
						fwrite($handle, '
	/**
	 * @ORM\\Column(type="string", length='.$length.')
	 */
	private $'.$setAttribute[$k+1].';
	');
					}
					break;

				//<---------------------------------------------------------------------------------------------------------------------------
				case "integer":
				$arrayReverse = array_reverse($checkTypeBox);
				$countArrayReverse = count($arrayReverse);
				//pour chaque attribut, un array de checkbox se créer,
				//si jamais le checkbox est vide, isset() affiche Null, empty() ne fonctionne pas
				if(isset($arrayReverse[0]) == false){
					//Affichera uniquement (type="integer")
						fwrite($handle, '
	/**
	 * @ORM\Column(type="integer")');
					//A partir de cette endroit, je fais la différence avec les checkbox Null (affiché nL dans l'array)
					// ([?] != "nL" ? OK : == "nL")
				}elseif(isset($arrayReverse[0]) == true && $arrayReverse[0] != "nL"){

					fwrite($handle, '
	/**
	 * @ORM\Column(type="integer")');

					for($countCheckTypeBox = 0; $countCheckTypeBox < $countArrayReverse; $countCheckTypeBox++){

						switch ($arrayReverse[$countCheckTypeBox]) {
							//Affichera (type="integer") -> * Id // strategy="AUTO"
							case "pK":
								fwrite($handle, '
	 * @ORM\Id');
								break;
							case "aT":
								fwrite($handle, '
	 * @ORM\GeneratedValue(strategy="AUTO")');
						}
					}
				}else{
					for($countCheckTypeBox = 0; $countCheckTypeBox < $countArrayReverse; $countCheckTypeBox++){

						switch ($arrayReverse[$countCheckTypeBox]) {
							//Affichera (type="integer", nullable=true) -> * \Id || \strategy="AUTO"
							case "nL":
								fwrite($handle, '
	/**
	 * @ORM\Column(type="integer", nullable=true)');

								break;
							case "pK":
								fwrite($handle, '
	 * @ORM\Id');
								break;
							case "aT":
								fwrite($handle, '
	 * @ORM\GeneratedValue(strategy="AUTO")');
						}
					}
				}
					fwrite($handle, '
	 */
	private $'.$setAttribute[$k+1].';
					');
					break;

					//<-----------------------------------------------------------------------------------------------------------------------
				case "text":
					//Il se passe la même chose que string cad affichera (type="integer") ou (type="integer", nullable=true)
					if(isset($checkTypeBox[0]) == "nL" 
					&& $checkEntite != $listeNomEntite[$j])
					{
							fwrite($handle, '
	/**
	 * @ORM\\Column(type="text", nullable=true)
	*/
	private $'.$setAttribute[$k+1].';
	');
					}else{
						fwrite($handle, '
	/**
	 * @ORM\\Column(type="text")
	 */
	private $'.$setAttribute[$k+1].';
	');
					}
					break;

					//<-----------------------------------------------------------------------------------------------------------------------
				case "boolean":

					//Même chose que la partie integer

					//Pour respecter les standards d'affichages Doctrine via le switch à venir, l'array doit être inversé
					$arrayReverse = array_reverse($checkTypeBox);
					$countArrayReverse = count($arrayReverse);
					if(isset($arrayReverse[0]) == false){
							fwrite($handle, '
	/**
	 * @ORM\Column(type="boolean")');

						array_push($construct, "is_nTbLnL!".$setAttribute[$k+1]);
					}elseif(isset($arrayReverse[0]) == true && $arrayReverse[0] != "nL") {
						$new = "";
						for($countCheckTypeBox = 0; $countCheckTypeBox < $countArrayReverse; $countCheckTypeBox++){
							if($arrayReverse[$countCheckTypeBox] == "bL" && $new != $setAttribute[$k+1]){
								fwrite($handle, '
	/**
	 * @ORM\Column(type="boolean", options={"default":true})');
								$new = $setAttribute[$k+1];
								array_push($construct, "is_bL!".$setAttribute[$k+1]);
							}
						}
					}else{
						for($countCheckTypeBox = 0; $countCheckTypeBox < $countArrayReverse; $countCheckTypeBox++){
							if($arrayReverse[$countCheckTypeBox] == "nL" && isset($arrayReverse[$countCheckTypeBox+1]) == "bL"){
								fwrite($handle, '
	/**
	 * @ORM\Column(type="boolean", nullable=true, options={"default":true})');

								array_push($construct, "is_bLnL!".$setAttribute[$k+1]);

							}elseif($arrayReverse[$countCheckTypeBox] === "nL"){
								fwrite($handle, '
	/**
 	 * @ORM\Column(type="boolean", nullable=true)');
								array_push($construct, "is_nL!".$setAttribute[$k+1]);
							}else{//}
						}
					}
				}
					fwrite($handle, '
	 */
	private $'.$setAttribute[$k+1].';
					');
					break;
				}
			//<------------------------------------------------------------------------------------------
			//<------------------------------------------------------------------------------------------
			//<------------------------------------------------------------------------------------------
			}
		}

		//On passe à la partie association (UNIDIRECTIONNELLE)
		if(!empty($setAssociation)){
			for($m = 0; $m < count($setAssociation); $m++){
				switch ($setAssociation[$m]) {
					case "n,1":
						//Détecter le nom de l'entité est important pour faire la différence entre chaque entité dans l'array $setAssociation
						if($setAssociation[$m-1] == $listeNomEntite[$j]){
							//NameForeignKey et ReferencedColumnName doivent être sélectionnés
							if($setAssociation[$m+2] == "NameForeignKey" || $setAssociation[$m+3] == "ReferencedColumnName"){
								$this->get('session')->getFlashBag()->clear();
					            $this->addFlash(
					                'error',
					                "la clé étrangère ou clé primaire doit être sélectionné dans l'association."
					            );
					            return $this->redirectToRoute('guillaume_auto_db_attribut');
							}else{
								//Association unidirectionnelle
								fwrite($handle,'
	/**
	* @ORM\ManyToOne(targetEntity="'.$setAssociation[$m+1].'")
	* @ORM\JoinColumn(name="'.$setAssociation[$m+1]."_".$request->request->get($setAssociation[$m+2]).'", referencedColumnName="'.$request->request->get($setAssociation[$m+3]).'")
	*/
	private $'.$setAssociation[$m+1].';
					');
								array_push($construct, "ManyToOne!".$setAssociation[$m+1]);
							}
						}
						break;
					case "1,1":
					//Même chose que n,1
						if($setAssociation[$m-1] == $listeNomEntite[$j]){
							if($setAssociation[$m+2] == "NameForeignKey" || $setAssociation[$m+3] == "ReferencedColumnName"){
								$this->get('session')->getFlashBag()->clear();
					            $this->addFlash(
					                'error',
					                "la clé étrangère ou clé primaire doit être sélectionné dans l'association."
					            );
					            return $this->redirectToRoute('guillaume_auto_db_attribut');
							}else{
								fwrite($handle, '
	/**
	 * @ORM\OneToOne(targetEntity="'.$setAssociation[$m+1].'")
	 * @ORM\JoinColumn(name="'.$setAssociation[$m+1]."_".$request->request->get($setAssociation[$m+2]).'", referencedColumnName="'.$request->request->get($setAssociation[$m+3]).'")
	 */
	private $'.$setAssociation[$m+1].';
							');
							}
						}
						break;
					case "1,n":
					//http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
						if($setAssociation[$m+2] == "NameForeignKey" || $setAssociation[$m+3] == "ReferencedColumnName"){
								$this->get('session')->getFlashBag()->clear();
					            $this->addFlash(
					                'error',
					                "la clé étrangère ou clé primaire doit être sélectionné dans l'association."
					            );
					            return $this->redirectToRoute('guillaume_auto_db_attribut');
							}else{
								if($setAssociation[$m-1] == $listeNomEntite[$j]){
													fwrite($handle, '
	/**
	 * @ORM\ManyToMany(targetEntity="'.$setAssociation[$m+1].'")
	 * @ORM\JoinTable(name="'.$setAssociation[$m-1].'_'.$setAssociation[$m+1].'",');
						for($l = 0; $l < count($setCheckBox); $l++){
							if($setAssociation[$m-1] == $setCheckBox[$l] && substr($setCheckBox[$l+1], 0, 2) == "pK"){
								fwrite($handle, '
	 *	joinColumns={@ORM\JoinColumn(name="'.$listeNomEntite[$j]."_".$setCheckBox[$l+2].'", referencedColumnName="'.$setCheckBox[$l+2].'")},
	 *	inverseJoinColumns={@ORM\JoinColumn(name="'.$setAssociation[$m+1]."_".$request->request->get($setAssociation[$m+2]).'", referencedColumnName="'.$request->request->get($setAssociation[$m+3]).'", unique=true)}
	 *	)
	 */
	private $'.$setAssociation[$m+1].';
		');
								array_push($construct, $setAssociation[$m+1]);
								}
							}

						}
					}
					break;
				case "n,n":
				//http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
					if($setAssociation[$m+2] == "NameForeignKey" || $setAssociation[$m+3] == "ReferencedColumnName"){
									$this->get('session')->getFlashBag()->clear();
						            $this->addFlash(
						                'error',
						                "la clé étrangère ou clé primaire doit être sélectionné dans l'association."
						            );
						            return $this->redirectToRoute('guillaume_auto_db_attribut');
								}else{
									if($setAssociation[$m-1] == $listeNomEntite[$j]){
							fwrite($handle, '
	/**
	 * @ORM\ManyToMany(targetEntity="'.$setAssociation[$m+1].'")
	 * @ORM\JoinTable(name="'.$setAssociation[$m-1].'_'.$setAssociation[$m+1].'",');
					for($l = 0; $l < count($setCheckBox); $l++){
						if($setAssociation[$m-1] == $setCheckBox[$l] && substr($setCheckBox[$l+1], 0, 2) == "pK"){
							fwrite($handle, '
	 *	joinColumns={@ORM\JoinColumn(name="'.$listeNomEntite[$j]."_".$setCheckBox[$l+2].'", referencedColumnName="'.$setCheckBox[$l+2].'")},
	 *	inverseJoinColumns={@ORM\JoinColumn(name="'.$setAssociation[$m+1]."_".$request->request->get($setAssociation[$m+2]).'", referencedColumnName="'.$request->request->get($setAssociation[$m+3]).'")}
	 *	)
	 */
	private $'.$setAssociation[$m+1].';
						');
									array_push($construct, $setAssociation[$m+1]);
									}
								}
							}
						}
					break;
				}
			}
				$countConstruct = count($construct);
				if($countConstruct >= 1){
					fwrite($handle, '
	public function __construct(){
				');

					//On fait la différence entre les types de booléens à enregistrer dans le constructeur

					$new = "";
					for($n = 0; $n < $countConstruct; $n++){
						if(substr($construct[$n], 0, 6) == "is_bL!" && $new != $construct[$n]){
							fwrite($handle, '
		$this->'.substr($construct[$n], strpos($construct[$n], "is_bL!")+6).' = true;');
							$new = $construct[$n];
						}elseif(substr($construct[$n], 0, 8) == "is_bLnL!"){
							fwrite($handle, '
		$this->'.substr($construct[$n], strpos($construct[$n], "is_bLnL!")+8).' = true;');
						}elseif(substr($construct[$n], 0, 6) == "is_nL!"){
							fwrite($handle, '
		$this->'.substr($construct[$n], strpos($construct[$n], "is_bLnL!")+6).' = false;');
						}elseif(substr($construct[$n], 0, 10) == "is_nTbLnL!"){
							fwrite($handle, '
		$this->'.substr($construct[$n], strpos($construct[$n], "is_nTbLnL!")+10).' = false;');
						}elseif(substr($construct[$n], 0, 10) != "ManyToOne!"){
							fwrite($handle, '
		$this->'.$construct[$n].' = new ArrayCollection();');
						}
						else{
							//
						}
					}
					fwrite($handle, '
	}');
				}
		}

				//Boucle pour écrire des get/set ordinaires
				for($k = 0; $k < count($setAttribute); $k++){
					if($setAttribute[$k] == $listeNomEntite[$j] ){
						fwrite($handle, '
	/**
	* Get '.$setAttribute[$k+1].'
	* @return '.$setAttribute[$k+2].'
	*/
	public function get'.$setAttribute[$k+1].'()
	{
		return $this->'.$setAttribute[$k+1].';
	}

	/**
	 * @param '.$setAttribute[$k+2].' $'.$setAttribute[$k+1].'
	 * @return '.$setAttribute[$k+2].'
	 */
	public function set'.$setAttribute[$k+1].'($'.$setAttribute[$k+1].')
	{
		$this->'.$setAttribute[$k+1].' = $'.$setAttribute[$k+1].';
		return $this;
	}
					');
					}

				}
		if(!empty($setAssociation)){
				for($n = 0; $n < $countConstruct; $n++){

					$manyToOne = substr($construct[$n], strpos($construct[$n], "ManyToOne!")+10);
					if(substr($construct[$n], 0, 10) == "ManyToOne!"){
						fwrite($handle, '
	public function get'.$manyToOne.'()
	{
		return $this->'.$manyToOne.';
	}

	public function set'.$manyToOne.'($'.$manyToOne.')
	{
		$this->'.$manyToOne.' = $'.$manyToOne.';
		return $this;
	}
					');
					}

					$old = null;
					//On détecte les premiers caractères qui ont été ajoutés dans $construct
					if(substr($construct[$n], 0, 6) != "is_bL!"
						&& substr($construct[$n], 0, 8) != "is_bLnL!"
						&& substr($construct[$n], 0, 6) != "is_nL!"
						&& substr($construct[$n], 0, 10) != "is_nTbLnL!"
					){
						if(empty($setAssociation) || isset($setAssociation)){
							for($m = 0; $m < count($setAssociation); $m++){
								if($setAssociation[$m] == $listeNomEntite[$j]
									&& ($setAssociation[$m+1] == "1,n"
									|| $setAssociation[$m+1] == "1,1"
									|| $setAssociation[$m+1] == "n,n"
									|| $setAssociation[$m+1] == "n,1"
									)
								){
									if(substr($construct[$n], 0, 10) != "ManyToOne!" && ($old == null || $old != $old)){

									//création des fonctions Add, Remove et Get pour chaque association
									fwrite($handle, '
	/**
	 * Add '.$construct[$n].'
	 * @param \\'.$request->request->get("bundleName").($request->request->get("directoryName")!=NULL?'\\':NULL).$request->request->get("directoryName").'\\Entity\\'.$setAssociation[$m+2].' $'.$construct[$n].'
	 * @return '.$listeNomEntite[$j].'
	 */
	public function add'.$construct[$n].'(\\'.$request->request->get("bundleName").'\\'.($request->request->get("directoryName")!=NULL?'\\':NULL).'Entity\\'.$setAssociation[$m+2].' $'.$construct[$n].')
	{
		$this->'.$construct[$n].'[] = $'.$construct[$n].';
		return $this;
	}

	/**
	 * Remove '.$construct[$n].'
	 * @param \\'.$request->request->get("bundleName").'\\'.($request->request->get("directoryName")!=NULL?'\\':NULL).'Entity\\'.$construct[$n].' $'.$construct[$n].'
	 */
	public function remove'.$construct[$n].'(\\'.$request->request->get("bundleName").'\\'.($request->request->get("directoryName")!=NULL?'\\':NULL).'Entity\\'.$setAssociation[$m+2].' $'.$construct[$n].')
	{
		$this->'.$construct[$n].'->removeElement($'.$construct[$n].');
	}

	/**
	 * Get '.$construct[$n].'
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function get'.$construct[$n].'()
	{
		return $this->'.$construct[$n].';
	}
	');
	$old = 'Add'.$construct[$n];
									}
								}
							}
						}
					}
				}
	}
						fwrite($handle, "
}"
	);
						fclose($handle);
						if($zipCreate === true){
							$zip->addFile($filePath, $listeNomEntite[$j].".php");
						}else{
							$this->get('session')->getFlashBag()->clear();
					        $this->addFlash(
					            'error',
					            "Impossible d'ouvrir l'archive Zip : $zipPath."
					        );
					        return $this->redirectToRoute('guillaume_auto_db_attribut');
						}

						//On stock les chemins de chaque fichier entité.
						array_push($stockHandle, $filePath);
					}
				}
			$zip->close();
			if (!copy(sys_get_temp_dir()."/autoDb.zip", "autoDb.zip")) {
			    echo "La copie $file du fichier a échoué...\n";
			}
			unlink(sys_get_temp_dir()."/autoDb.zip");
			foreach ($stockHandle as $key => $value) {
				unlink($value);
			}
			return $this->redirectToRoute('guillaume_auto_db_uploadEntity',
				array('stockHandle' => $stockHandle)
			);
        }
		return $this->render('GuillaumeAutoDbBundle:Automating:GetEntity.html.twig');
	}

	public function UploadEntityAction(Request $request){
		$stockHandle = $request->get('stockHandle');
		$zipPath = "autoDb.zip";
		//On essaye d'ouvrir le fichier pour vérifier s'il existe.
		$zipExist = "autoDb.zip";
		if(!file_exists($zipExist)){
			$this->get('session')->getFlashBag()->clear();
	        $this->addFlash(
	            'error',
	            "L'outil n'a pu trouver l'archive zip, vérifiez son existence dans le dossier temporaire système ou dans le dossier /web de Symfony, vérifiez également les droits d'écritures et de lectures dans lesdits dossiers."
	        );
	        return $this->redirectToRoute('guillaume_auto_db_attribut');
		}

		$handle = fopen($zipExist, "r") or die("Impossible d'ouvrir l'archive zip, cet archive se trouve dans votre dossier /web/ de Symfony ou dans le dossier temporire du système.");
		if($handle === false){
			print_r("Impossible d'ouvrir l'archive zip, cet archive se trouve dans votre dossier /web/ de Symfony ou dans le dossier temporire du système.");
		}
		fclose($handle);
		return $this->render('GuillaumeAutoDbBundle:Automating:UploadEntity.html.twig',
			array('zip' => $zipExist
			)
		);
	}

	public function setAttribute($postKey, $postValue, $listeNomEntite){
		$attribut = array();
		//Variables pour initialiser les count
		$countPostKey = count($postKey);
		$countListeNomEntite = count($listeNomEntite);

		for($i = 0; $i < $countPostKey; $i++){
			//Supprime le dernier _x et ce qu'il y a après
			$nameAttribute = substr($postKey[$i], 0
					, strpos( $postKey[$i], "_x"));
			//Compte le nombre d'entité existante
			for($j = 0; $j < $countListeNomEntite; $j++){
				switch($nameAttribute){
					case $listeNomEntite[$j]:
						array_push($attribut, $listeNomEntite[$j]);
						array_push($attribut, $postValue[$i]);
						break;
					case $listeNomEntite[$j]."Type":
						array_push($attribut, $postValue[$i]);
						break;
					case "string".$listeNomEntite[$j]."Type":
						array_push($attribut, $postKey[$i]);
						array_push($attribut, $postValue[$i]);
						break;
				}
			}
		}
		return $attribut;
	}

	public function setCheckBox($postKey, $postValue, $listeNomEntite){
		$attribut = array();

		//Variables pour initialiser les count
		$countPostKey = count($postKey);
		$countListeNomEntite = count($listeNomEntite);
		for($i = 0; $i < $countPostKey; $i++){
			//Mémorise l'élément _xN
			$xAttribute = strstr($postKey[$i], "_x");
			
			//Supprime le dernier _x et ce qu'il y a après
			$nameAttribute = substr($postKey[$i], 0
					, strpos( $postKey[$i], "_x"));
			for($j = 0; $j < $countListeNomEntite; $j++){
				//Prend les 2 premiers caractères afin de détécter le type de checkbox
				$nomEntite = substr($nameAttribute, 0, 2).$listeNomEntite[$j];

				//On vérifie si le nom de l'attribut est bien par exemple une Primary Key, ex: PkName = PkName
				if($nameAttribute == $nomEntite){				
					$a = 0;
					//On garde en mémoire le $i via $a, le -2, -3 permet de reculer dans le tableau afin de trouver le nommage de l'input
					//Switch bug avec le $a, à re vérifier
					if($postKey[$a = $i-2] == substr($nameAttribute, 2).$xAttribute || 
						$postKey[$a = $i-3] == substr($nameAttribute, 2).$xAttribute ||
						$postKey[$a = $i-4] == substr($nameAttribute, 2).$xAttribute
					){
						array_push($attribut, substr($postKey[$a], 0
							, strpos( $postKey[$a], "_x")));
						//Enregistre la key checkbox
						array_push($attribut, $nameAttribute);
						array_push($attribut, $postValue[$a]);
					}
				}
			}
		}
		return $attribut;
	}

	function setAssociation($postKey, $postValue, $listeNomEntite){
		$attribut = array();

		//Variables pour initialiser les count
		$countPostKey = count($postKey);
		$countListeNomEntite = count($listeNomEntite);
		//La valeur vide permet de reculer dans l'array sans déclencher d'erreur.
		array_push($attribut, "Vide0", "Vide1", "Vide2", "Vide3");
		$listeCardinalite = ["Aucune", "1,n", "n,1", "n,n", "1,1"];
		$final = array();
		//memo va permettra la mémorisation du nom de l'entite avec ses associations, exemple = memo : EntiteA; 1,n, Entite B, 1,n Entite C, 1,n EntiteD...
		$memo = "";
		for($i = 0; $i < $countPostKey; $i++){
			//Supprime le dernier _x et ce qu'il y a après
			$nameAttribute = substr($postKey[$i], 0
				, strpos( $postKey[$i], "_x"));
			$findAssociation = strstr($nameAttribute, "Association");
			for($j = 0; $j < $countListeNomEntite; $j++){
				if($nameAttribute == $listeNomEntite[$j]){
					array_push($attribut, $listeNomEntite[$j]);
				}
			}
			if($findAssociation == "Association"){
				array_push($attribut, $postValue[$i]);
			}
		}

		for($j = 0; $j < count($attribut); $j++){
			if($attribut[$j] == "Aucune"){
				$key = array_search($attribut[$j], $attribut);
				if ($key != NULL || $key !== FALSE) {
				    unset($attribut[$key]);
				    $attribut2 = array_values($attribut);
				}
			}else{
				$attribut2 = array_values($attribut);
			}
		}
	    for($k = 0; $k < count($attribut2); $k++){
			for($l = 0; $l < count($listeCardinalite); $l++){
				//On vérifie la valeur de la cardinalité afin d'enregistrer l'entite parente et le choix d'entité, Exemple k = 1,n, l'array attribut contient le nom de l'entité et son association, k-1 = EntiteA, k+1 = EntiteB(choix de l'association)
				//echo $listeCardinalite[$l];
				if($listeCardinalite[$l] == $attribut2[$k]){
					//Le choix aucune ne peut être une association
					if($attribut2[$k] != "Aucune"){
						//Initialise la première association avec une variable memo et une valeur "Vide", l'absence de "Vide" pourrait déclencher une erreur offset.
						if($memo == "" && $attribut2[$k-5] == "Vide0" || $memo == "" && $attribut2[$k-5]  != "Vide0" && $attribut2[$k+3] != null){
							$memo = $attribut2[$k-1];
							array_push($final, $memo);
							array_push($final, $attribut2[$k]);
							array_push($final, $attribut2[$k+1]);
							array_push($final, $attribut2[$k+2]);
							array_push($final, $attribut2[$k+3]);
							
						}//Recule de 4 cases pour vérifier si la valeur est une cardinalité ou une entité avec l'aide de ","
						elseif(strstr($attribut2[$k-4],",")){
							array_push($final, $memo);
							array_push($final, $attribut2[$k]);
							array_push($final, $attribut2[$k+1]);
							array_push($final, $attribut2[$k+2]);
							array_push($final, $attribut2[$k+3]);
						}//Permet d'enregistrer les associations après la toute première, sans elle, setAssociation enregistrera uniquement la première.
						else/*(!strstr($attribut[$k-4],",") && $memo != $attribut[$k-1])*/{
							$memo = $attribut2[$k-1];
							array_push($final, $memo);
							array_push($final, $attribut2[$k]);
							array_push($final, $attribut2[$k+1]);
							array_push($final, $attribut2[$k+2]);
							array_push($final, $attribut2[$k+3]);
						}
					}
				}
			}
	    }

		
		return $final;
	}
}
?>
