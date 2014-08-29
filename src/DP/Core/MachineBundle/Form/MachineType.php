<?php

/*
** Copyright (C) 2010-2013 Kerouanton Albin, Smedts Jérôme
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along
** with this program; if not, write to the Free Software Foundation, Inc.,
** 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace DP\Core\MachineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ip', 'text', array('label' => 'machine.fields.ip'))
            ->add('publicIp', 'text', array('label' => 'machine.fields.public_ip', 'required' => false))
            ->add('port', 'number', array('label' => 'machine.fields.port'))
            ->add('username', 'text', array('label' => 'machine.fields.username'))
            ->add('password', 'password', array('label' => 'machine.fields.password'))
            ->add('groups', 'dedipanel_group_assignement')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form    = $event->getForm();
            $machine = $event->getData();

            if ($machine->getId() !== null) {
                $form->add('password', 'password', array(
                    'label'    => 'machine.fields.password',
                    'required' => false,
                ));
            }
        });
    }

    public function getName()
    {
        return 'dedipanel_machine';
    }
}
