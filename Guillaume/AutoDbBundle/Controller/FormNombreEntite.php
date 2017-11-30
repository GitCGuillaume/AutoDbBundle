<?php
namespace Guillaume\AutoDbBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DefaultController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormNombreEntite extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder
		->add('nombreEntite', IntegerType::class)
		->add('save', SubmitType::class, array(
			'attr' =>array('class' => 'form-control')
		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Guillaume\AutoDbBundle\Entity\NombreEntite',
	    ));
	}

}

?>
