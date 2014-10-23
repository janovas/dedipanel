<?php

namespace DP\Core\GameBundle\Form;

use DP\Core\GameBundle\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'game.fields.name'))
            ->add('installName', null, array('label' => 'game.fields.installName'))
            ->add('launchName', null, array('label' => 'game.fields.launchName'))
            ->add('bin', null, array('label' => 'game.fields.bin'))
            ->add('binDir', null, array('label' => 'game.fields.binDir'))
            ->add('orangebox', null, array('label' => 'game.fields.isOrangebox', 'required' => false))
            ->add('source', null, array('label' => 'game.fields.isSource', 'required' => false))
            ->add('steamCmd', null, array('label' => 'game.fields.isSteamCmd', 'required' => false))
            ->add('appId', null, array('label' => 'game.fields.appId'))
            ->add('appMod', null, array('label' => 'game.fields.appMod'))
            ->add('map', null, array('label' => 'game.fields.map'))
            ->add('configTemplate', null, array('label' => 'game.fields.configTemplate'))
            ->add('sourceImagesMaps', null, array('label' => 'game.fields.sourceImagesMaps'))
            ->add('plugins', null, array('label' => 'game.fields.plugins', 'required' => false))
            ->add('type', 'choice', array(
                'choices' => Game::getTypeList(),
                'label' => 'game.fields.type', 
            ))
            ->add('available', null, array('label' => 'game.fields.available', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DP\Core\GameBundle\Entity\Game'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dedipanel_game';
    }
}
