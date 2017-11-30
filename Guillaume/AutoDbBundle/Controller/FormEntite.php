<?php
namespace Guillaume\AutoDbBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FormEntite extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder
		->add('nomEntite', TextType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Guillaume\AutoDbBundle\Entity\Entite',
	    ));
	}
}
?>