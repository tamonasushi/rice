<?php
class Usuario extends Illuminate\Database\Eloquent\Model 
{

	protected $table = 'usuario';
	
	public $timestamps = false;

	private $role = 'invitado';

	private $aclNavigation = array();

    const SALT = "d0be2dc421be4fcd0172e5afceea3970e2f3d940";

	public static function encriptPassword($password){
		return md5(md5(sha1($password)));
	}

	public function persona(){
		return $this->belongsTo('Persona','id_persona','id')->with("direccion");
	}

    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getAclNavigation()
    {
          $this->aclNavigation[$this->role] = new Rice_Acl($this->role, 'acl.ini');
          return $this->aclNavigation[$this->role];
    }



}