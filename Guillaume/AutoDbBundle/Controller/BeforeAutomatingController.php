<?php

namespace Guillaume\AutoDbBundle\Controller;
use Guillaume\AutoDbBundle\Entity\NombreEntite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class BeforeAutomatingController extends Controller
{
    public function indexAction(Request $request)
    {
    	$nbEntite = new NombreEntite();
    	$form = $this->createForm(FormNombreEntite::class, $nbEntite);
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()){
    		$nbEntite = $form->getData()->getNombreEntite();
    		
    		return $this->redirectToRoute('guillaume_auto_db_entite', array('nbEntite' => $nbEntite));
    	}


        return $this->render('GuillaumeAutoDbBundle:BeforeAutomating:Index.html.twig',
            array('form' => $form->createView()
           	)
        );
    }

    public function entiteAction(Request $request){
    	//get nb entite
		$nbEntite = $request->get('nbEntite');

        //Vérifie si valeur entier
        if(!preg_match('/^\d+$/', $nbEntite)){
            $this->get('session')->getFlashBag()->clear();
            $this->addFlash(
                'error',
                'Le paramètre doit être un entier.'
            );

            return $this->redirectToRoute('guillaume_auto_db_homepage');
        }

		//variables dynamiques
		$listeNomEntite = array();
		for($i = 0; $i < $nbEntite; $i++){
			array_push($listeNomEntite, "listeNomEntite".$i);
			$i+1;
		}

		//Creation formulaire
		$defaultData = array();
		$form = $this->createFormBuilder($defaultData);

		//parcours array listeNomEntite
		foreach ($listeNomEntite as $key) {
			$form->add($key, TextType::class, array(
				'label' => 'Nom entité : ',
			));
		}
			$form->add('submit', SubmitType::class, array(
				'label' => 'Envoyer'
			));
		$frm = $form->getForm();

        //On poste le formulaire et on le stock dans une session
        if ($request->isMethod('POST')){
            $frm->submit($request->request->get($frm->getName()));

            if ($frm->isSubmitted() && $frm->isValid()){
                $data = $frm->getData();
                $session = new Session();
                $session->set('data', $data);
                //Redirection vers la page attribut dans le dossier default
                return $this->redirectToRoute('guillaume_auto_db_attribut');
            }else{
                //Notice une erreur si le formulaire n'a pas été respecté.
                $this->get('session')->getFlashBag()->clear();
                $this->addFlash(
                    'error',
                    'Une erreur est survenue.'
                );
                var_dump($frm->isValid());
                
                return $this->redirectToRoute('guillaume_auto_db_entite');
            }
        }

		return $this->render('GuillaumeAutoDbBundle:BeforeAutomating:Entite.html.twig',
            array('form' => $frm->createView()
           	)
        );
	}

    public function attributAction(Request $request){
        $session = $request->getSession();

        if($session->get('data') == NULL){
            //Envoit une erreur si le formulaire n'a pas été respecté.
                $this->get('session')->getFlashBag()->clear();
                $this->addFlash(
                    'error',
                    'Les entités n\'ont pas été trouvées.'
                );
                return $this->redirectToRoute('guillaume_auto_db_entite');
        }
        $entite = array();

        foreach ($session->get('data') as $key) {
            //On vérifie si les entrées sont des caractères autorisés.
            if(preg_match("/^[a-zA-Z0-9_-]+$/", $key) == false) {
                $this->get('session')->getFlashBag()->clear();
                $this->addFlash(
                    'error',
                    'Les entités doivent contenir des caractères entiers ou alphabétiques.'
                );
                return $this->redirectToRoute('guillaume_auto_db_homepage');
            }
            array_push($entite, $key);
        }
        //On créait un nouveau token
        if($request->request->get('_token')==null){
            $token = bin2hex(random_bytes(32));
            $session->set('tokOk', $token);
        }
        //On prend la clé et on affiche leur valeur
        if ($request->isMethod('POST')){
            for($i=0; $i< count($request->request); $i++){
                echo '<pre>';
                var_dump("key : ",$key = $request->request->keys()[$i]);
                echo '</pre>';
                echo '<pre>';
                var_dump("résultat : ",$request->request->get($key));
                echo '</pre>';
            }
            if ($this->isCsrfTokenValid('token_id', $request->request->get('_csrf_token'))) {
                echo "manger";
            }

        }
        

        return $this->render('GuillaumeAutoDbBundle:BeforeAutomating:Attribut.html.twig',
            array('entite' => $entite,
                    'token' =>$session->get('tokOk'),
            )
        );
    }
}
