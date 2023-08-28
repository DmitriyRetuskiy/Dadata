<?php

namespace App\Core\Domain\Model;

/**
 * @Entity @Table (name="persons")
 **/
class Peron
{
    /**
     * @ORM\Id  @Column(name="id_user", type="integer") @GeneratedValue
     **/
    protected $id_user;
    /**
     * @ORM\Column(name="name", type="string") @GeneratedValue
     **/
    protected $name;
    /**
     * @ORM\Column(name="sername", type="string") @GeneratedValue
     **/
    protected $sername;
    /**
     * @ORM\Column (name="number", type="integer") @GeneratedValue
     **/
    protected $number;


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSername()
    {
        return $this->sername;
    }

    /**
     * @param mixed $sername
     */
    public function setSername($sername): void
    {
        $this->sername = $sername;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

}


