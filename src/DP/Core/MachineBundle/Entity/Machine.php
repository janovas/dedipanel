<?php

/**
 * This file is part of Dedipanel project
 *
 * (c) 2010-2015 Dedipanel <http://www.dedicated-panel.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DP\Core\MachineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DP\GameServer\GameServerBundle\Entity\GameServer;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use Dedipanel\PHPSeclibWrapperBundle\Server\Server;
use Dedipanel\PHPSeclibWrapperBundle\Connection\ConnectionInterface;
use DP\Core\MachineBundle\Validator\CredentialsConstraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use DP\VoipServer\VoipServerBundle\Entity\VoipServer;

/**
 * DP\Core\MachineBundle\Entity\Machine
 *
 * @ORM\Table(name="machine")
 * @ORM\Entity(repositoryClass="DP\Core\MachineBundle\Entity\MachineRepository")
 */
class Machine extends Server
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var integer $ip
     *
     * @ORM\Column(name="privateIp", type="string", length=15)
     */
    protected $ip;
    
    /**
     * @var integer $publicIp
     *
     * @ORM\Column(name="publicIp", type="string", length=15, nullable=true)
     */
    protected $publicIp;
    
    /**
     * @var integer $port
     *
     * @ORM\Column(name="port", type="integer")
     */
    protected $port = 22;
    
    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=16)
     */
    protected $username;
    
    /**
     * @var string $password
     */
    protected $password;
    
    /**
     * @var string $privateKeyName
     *
     * @ORM\Column(name="privateKeyName", type="string", length=23)
     */
    protected $privateKeyName;
    
    /**
     * @var string $home
     *
     * @ORM\Column(name="home", type="string", length=255)
     */
    protected $home;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $gameServers
     *
     * @ORM\OneToMany(targetEntity="DP\GameServer\GameServerBundle\Entity\GameServer", mappedBy="machine", cascade={"persist", "remove"})
     */
    protected $gameServers;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $gameServers
     *
     * @ORM\OneToMany(targetEntity="DP\VoipServer\VoipServerBundle\Entity\VoipServer", mappedBy="machine", cascade={"persist", "remove"})
     */
    protected $voipServers;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbCore", type="integer", nullable=true)
     */
    protected $nbCore;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is64bit", type="boolean")
     */
    protected $is64bit = false;
    
    /**
     * @ORM\ManyToMany(targetEntity="DP\Core\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="machine_to_groups",
     *      joinColumns={@ORM\JoinColumn(name="machine_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @var \Dedipanel\PHPSeclibWrapperBundle\Connection\ConnectionInterface $connection
     */
    protected $connection;
    
    
    public function __construct()
    {
        $this->gameServers = new ArrayCollection();
        $this->voipServers = new ArrayCollection();
        $this->groups      = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set publicIp
     *
     * @param integer $publicIp
     */
    public function setPublicIp($publicIp)
    {
        $this->publicIp = $publicIp;
    }

    /**
     * Get publicIp
     *
     * @return integer
     */
    public function getPublicIp()
    {
        if (empty($this->publicIp)) {
            return $this->ip;
        }

        return $this->publicIp;
    }

    /**
     * Get the ip used internally
     */
    public function getPrivateIp()
    {
        return $this->ip;
    }
    
    public function addGameServer(GameServer $srv)
    {
        $srv->setMachine($this);
        $this->gameServers[] = $srv;
    }

    public function getGameServers()
    {
        return $this->gameServers;
    }

    public function addVoipServer(VoipServer $srv)
    {
        $srv->setMachine($this);
        $this->voipServers[] = $srv;
    }

    public function getVoipServers()
    {
        return $this->voipServers;
    }

    /**
     * Set the number of core on the server
     *
     * @param integer $nbCore
     */
    public function setNbCore($nbCore)
    {
        $this->nbCore = $nbCore;
    }

    /**
     * Get the number of core on the server
     *
     * @return integer Number of core
     */
    public function getNbCore()
    {
        return $this->nbCore;
    }

    /**
     * Sets is 64 bit system
     *
     * @param boolean $is64bit Is 64 bit system ?
     *
     * @return Machine
     */
    public function setIs64Bit($is64bit)
    {
        $this->is64bit = $is64bit;

        return $this;
    }

    /**
     * Gets is 64 bit system
     *
     * @return boolean Is 64 bit system
     */
    public function is64Bit()
    {
        return $this->is64bit;
    }
    
    /**
     * Gets the groups granted to the user.
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }
    
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        
        return $this;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->username . '@' . $this->getPublicIp();
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('ip', new Assert\NotBlank(array('message' => 'machine.assert.ip.empty')));
        $metadata->addPropertyConstraint('ip', new Assert\Ip(array('message' => 'machine.assert.ip.not_valid')));
        $metadata->addPropertyConstraint('publicIp', new Assert\Ip(array('message' => 'machine.assert.publicIp')));
        $metadata->addPropertyConstraint('port', new Assert\NotBlank(array('message' => 'machine.assert.port.empty')));
        $metadata->addPropertyConstraint('port', new Assert\Range(array(
            'min' => 1, 
            'minMessage' => 'machine.assert.port.not_valid',
            'max' => 65536, 
            'maxMessage' => 'machine.assert.port.not_valid',
        )));
        $metadata->addPropertyConstraint('username', new Assert\NotBlank(array('message' => 'machine.assert.username')));
        $metadata->addConstraint(new Assert\Callback(array(
            'methods' => array('validateNotEmptyPassword'),
        )));
        $metadata->addConstraint(new CredentialsConstraint);
    }
    
    public function validateNotEmptyPassword(ExecutionContextInterface $context)
    {
        // Ne valide le champ "password" que s'il s'agit d'une nouvelle entité
        // (si le password est précisé lors de l'édition, la clé est régénéré)
        if (null === $this->getId() && null === $this->getPassword()) {
            $context->buildViolation('machine.assert.password')
                ->atPath('password')
                ->addViolation();
        }
    }
}
